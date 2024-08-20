<?php

namespace CapsulesCodes\InertiaMailable\Mail;

use Illuminate\Mail\Mailable as Base;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Response;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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

            return ( new CssToInlineStyles() )->convert( $this->getHtml(), $this->getCss() );
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

        $path = Config::get( 'inertia-mailable.manifest' );

        if( ! File::exists( $path ) ) throw new Exception( "Vite manifest not found." );

        $manifest = json_decode( File::get( $path ), true );

        if( ! Arr::has( $manifest, Config::get( 'inertia-mailable.js' ) ) && ! Arr::has( $manifest, Config::get( 'inertia-mailable.ts' ) ) ) throw new Exception( "File not found in manifest. Please run 'npm run build' or publish the preferred file." );

        $buffer = Arr::has( $manifest, Config::get( 'inertia-mailable.js' ) ) ? Config::get( 'inertia-mailable.js' ) : Config::get( 'inertia-mailable.ts' );

        $build = Config::get( 'inertia-mailable.build' );

        $file = "$build/{$manifest[ $buffer ][ 'file' ]}";

        return Process::path( App::basePath() )->run( [ "node", $file, json_encode( $data ) ], function( $type, $output ){ if( $type == 'err' ) throw new Exception( Str::match( '/^.*Error: .*/m', $output ) ); } )->output();
    }

    protected function getHtml() : string
    {
        $data = $this->getData();


        $blade = Response::view( $data[ 'rootView' ], [ 'page' => $data ] )->getContent();

        $inertia = $this->getInertia( $data );


        $crawler = new Crawler( $blade );

        $html = Str::replace( $crawler->filter( '#' . Config::get( 'inertia-mailable.inertia' ) )->first()->outerHtml(), json_decode( $inertia, true )[ 'body' ], $crawler->first()->outerHtml() );

        return preg_replace('/>\s+</', '><', html_entity_decode( $html ) );
    }

    protected function getCss() : string | null
    {
        if( File::exists( Config::get( 'inertia-mailable.css' ) ) )
        {
            $css = File::get( Config::get( 'inertia-mailable.css' ) );
        }

        if( File::exists( App::basePath( 'node_modules/.bin/tailwind' ) ) )
        {
            $path = 'framework/mails';

            $disk = Storage::build( [ 'driver' => 'local', 'root' => storage_path() ] );

            if( ! $disk->exists( $path ) )
            {
                $disk->makeDirectory( $path );

                $disk->put( "{$path}/.gitignore", "*\n!.gitignore" );
            }

            $filename = "$path/" . Str::random( 40 );

            $disk->put( $filename , html_entity_decode( $this->getHtml() ) );

            $file = isset( $css ) ? Config::get( 'inertia-mailable.css' ) : dirname( __DIR__, 2 ) . '/stubs/css/mail.css';

            $css = Process::path( App::basePath() )->run( [ App::basePath( 'node_modules/.bin/tailwind' ), "-i", $file, "--content", $disk->path( $filename ) ] )->output();

            if( $disk->has( $filename ) ) $disk->delete( $filename );
        }

        return isset( $css ) ? preg_replace( '/\/\*[\s\S]*?\*\//', '', $css ) : null;
    }
}
