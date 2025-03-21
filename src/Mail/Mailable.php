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
use Illuminate\Support\Arr;
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

        if( File::exists( App::basePath( $file ) ) )
        {
            $inertia = App::basePath( $file );
        }

        $manifest = App::basePath( Config::get( 'inertia-mailable.manifest' ) );

        if( File::exists( $manifest ) )
        {
            $content = json_decode( File::get( $manifest ), true );

            $directory = dirname( $manifest );

            $path = Arr::get( Arr::get( $content,  $file ), 'file' );

            if( $path && File::exists( "$directory/$path" ) )
            {
                $inertia = "$directory/$path";
            }
        }

        if( ! isset( $inertia ) ) throw new Exception( "File not found at path : '{$file}'. Please run 'npm run build', publish file or modify config entries." );

        return $this->process( [ $inertia, json_encode( $data ) ] );
    }

    protected function getHtml() : string
    {
        $data = $this->getData();


        $isSSRenabled = Config::get( 'inertia.ssr.enabled', false );

        if( $isSSRenabled ) Config::set( 'inertia.ssr.enabled', false );

        $blade = Response::view( $data[ 'rootView' ], [ 'page' => $data ] )->getContent();

        if( $isSSRenabled ) Config::set( 'inertia.ssr.enabled', true );


        $inertia = json_decode( $this->getInertia( $data ), true )[ 'body' ];


        $crawler = new Crawler( $blade );

        $id = '#' . Config::get( 'inertia-mailable.id' );

        $this->html = Str::replace( $crawler->filter( $id )->first()->outerHtml(), $inertia, $crawler->first()->outerHtml() );

        return $this->html;
    }

    protected function getCss() : string | null
    {
        $file = Config::get( 'inertia-mailable.css' );

        if( File::exists( App::basePath( $file ) ) )
        {
            $css = File::get( App::basePath( $file ) );
        }

        $manifest = App::basePath( Config::get( 'inertia-mailable.manifest' ) );

        if( File::exists( $manifest ) )
        {
            $content = json_decode( File::get( $manifest ), true );

            $directory = dirname( $manifest );

            $path = Arr::get( Arr::get( $content,  $file ), 'file' );

            if( $path && File::exists( "$directory/$path" ) )
            {
                $css = File::get( "$directory/$path" );
            }
        }

        return $css ?? null;
    }


    private function convert( $html, $css ) : string
    {
        return preg_replace( [ '/>\s+</', '/<!--.*?-->/s', '/\sclass="[^"]*"/i' ] , [ '><', '', '' ], ( new CssToInlineStyles() )->convert( $html, $css ) );
    }

    private function process( array $command, Closure | null $callback = null ) : string
    {
        $process = Process::run( [ Config::get( 'inertia-mailable.node' ), ...$command ], $callback );

        if( $process->failed() ) throw new Exception( $process->errorOutput() );

        return $process->output();
    }
}
