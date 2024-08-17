<?php

namespace CapsulesCodes\InertiaMailable\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;


class InertiaMailableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $stubsPath =  dirname( __DIR__, 2 ) . '/stubs';
        $configPath = dirname( __DIR__, 2 ) . '/config';


        $this->loadViewsFrom( "$stubsPath/views", 'inertia-mailable' );
        $this->mergeConfigFrom( "$configPath/inertia-mailable.php", 'inertia-mailable' );


        $this->publishes( [ "$stubsPath/config/inertia-mailable.php" => App::configPath( 'inertia-mailable.php' ) ], 'config' );
        $this->publishes( [ "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ) ], 'css' );
        $this->publishes( [ "$stubsPath/js/vue/mail.js" => App::resourcePath( 'js/mail.js' ) ], 'vue-js' );
        $this->publishes( [ "$stubsPath/ts/vue/mail.ts" => App::resourcePath( 'ts/mail.ts' ) ], 'vue-ts' );
        $this->publishes( [ "$stubsPath/ts/mail.blade.php" => App::resourcePath( 'views/mail.blade.php' ) ], 'blade' );
    }
}
