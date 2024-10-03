<?php

namespace CapsulesCodes\InertiaMailable\Mail;

use Illuminate\Mail\Mailable as Base;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Response;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Closure;
use Exception;


class Mailable extends Base
{
    public string $root;

    public array $propsData = [];


    public function root( $root ) : self
    {
        $this->root = $root;

        return $this;
    }

    public function props( $key, $value = null )
    {
        if( is_array( $key ) )
        {
            $this->propsData = array_merge( $this->propsData, $key );
        }
        else
        {
            $this->propsData[ $key ] = $value;
        }

        return $this;
    }

    public function view( $view, array $data = [] ) : self
    {
        $this->view = $view;

        $this->propsData = array_merge( $this->propsData, $data );

        return $this;
    }

    public function render() : string
    {
        return $this->withLocale( $this->locale, function()
        {
            $this->prepareMailableForDelivery();

            $this->ensurePropsAreHydrated();

            return $this->convert( $this->getHtml(), $this->getCss() );
        } );
    }

    private function ensurePropsAreHydrated() : void
    {
        if( ! method_exists( $this, 'content' ) ) return;

        $content = $this->content();

        foreach( $content->props as $key => $value ) $this->props( $key, $value );
    }

    protected function buildView() : array
    {
        return array_filter( [ 'html' => new HtmlString( $this->render() ), 'text' => $this->textView ?? null ] );
    }

    protected function getRoot() : string
    {
        return isset( $this->root ) && ! empty( $this->root ) ? $this->root : 'inertia-mailable::mail';
    }

    protected function getData() : array
    {
        return [ 'component' => $this->view, 'props' => $this->propsData, 'rootView' => $this->getRoot(), 'viewData' => $this->viewData ];
    }

    protected function getInertia( array $data ) : string
    {
        if( ! isset( $data[ 'component' ] ) ) throw new Exception( "Component [{$data[ 'component' ]}] not found." );

        $file = Config::get( 'inertia-mailable.inertia' );

        if( ! File::exists( App::basePath( $file ) ) ) throw new Exception( "File not found at path : {$file}. Please run 'npm run build', publish file or modify config entries." );

        $callback = function( $type, $output ){ if( $type == 'err' ) throw new Exception( Str::match( '/(Error:.*|\\[Vue warn\\]:.*)/m', $output ) ); };

        return $this->process( [ App::basePath( $file ), json_encode( $data ) ], $callback );
    }

    protected function getHtml() : string
    {
        $data = $this->getData();


        $blade = Response::view( $data[ 'rootView' ], [ 'page' => $data ] )->getContent();

        $inertia = json_decode( $this->getInertia( $data ), true )[ 'body' ];


        $crawler = new Crawler( $blade );

        $id = '#' . Config::get( 'inertia-mailable.id' );

        $html = Str::replace( $crawler->filter( $id )->first()->outerHtml(), $inertia, $crawler->first()->outerHtml() );

        $this->html = preg_replace('/>\s+</', '><', html_entity_decode( $html ) );

        return $this->html;
    }

    protected function getCss() : string | null
    {
        if( File::exists( App::basePath( Config::get( 'inertia-mailable.css' ) ) ) )
        {
            $css = File::get( App::basePath( Config::get( 'inertia-mailable.css' ) ) );
        }

        if( File::exists( App::basePath( 'node_modules/.bin/tailwind' ) ) )
        {
            $command = [ App::basePath( 'node_modules/.bin/tailwind' ) ];


            $input = [ "-i", isset( $css ) ? App::basePath( Config::get( 'inertia-mailable.css' ) ) : dirname( __DIR__, 2 ) . '/stubs/css/mail.css' ];

            $command = array_merge( $command, $input );


            $path = 'framework/mails';

            $disk = Storage::build( [ 'driver' => 'local', 'root' => storage_path() ] );

            if( ! $disk->exists( $path ) )
            {
                $disk->makeDirectory( $path );

                $disk->put( "{$path}/.gitignore", "*\n!.gitignore" );
            }

            $filename = "$path/" . Str::random( 40 );

            $disk->put( $filename , $this->html );

            $content = [ "--content", $disk->path( $filename ) ];

            $command = array_merge( $command, $content );


            if( File::exists( App::basePath( Config::get( 'inertia-mailable.tailwind' ) ) ) )
            {
                $config = [ '--config', App::basePath( Config::get( 'inertia-mailable.tailwind' ) ) ];

                $command = array_merge( $command, $config );
            }

            $css = $this->process( $command );


            if( $disk->has( $filename ) ) $disk->delete( $filename );
        }

        return isset( $css ) ? preg_replace( '/\/\*[\s\S]*?\*\//', '', $css ) : null;
    }


    private function convert( $html, $css ) : string
    {
        return preg_replace( '/\sclass="[^"]*"/i', '',  ( new CssToInlineStyles() )->convert( $html, $css ) );
    }

    private function process( array $command, Closure | null $callback = null ) : string
    {
        $process = Process::run( [ Config::get( 'inertia-mailable.node' ), ...$command ], $callback );

        if( $process->failed() ) throw new Exception( $process->errorOutput() );

        return $process->output();
    }
}
