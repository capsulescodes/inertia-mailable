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


        $this->publishes( [ "$configPath/inertia-mailable.php" => App::configPath( 'inertia-mailable.php' ) ], 'inertia-mailable-config' );
        $this->publishes( [ "$stubsPath/ts/mail.blade.php" => App::resourcePath( 'views/mail.blade.php' ) ], 'inertia-mailable-blade' );
        $this->publishes( [ "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ) ], 'inertia-mailable-css' );


        $this->publishes( [

            "$stubsPath/js/vue/mail.js" => App::resourcePath( 'js/mail.js' ),
            "$stubsPath/js/vue/mails/Welcome.vue" => App::resourcePath( 'js/mails/Welcome.vue' )

        ], 'inertia-mailable-vue-js' );


        $this->publishes( [

            "$stubsPath/ts/vue/mail.ts" => App::resourcePath( 'ts/mail.ts' ),
            "$stubsPath/ts/vue/mails/Welcome.vue" => App::resourcePath( 'ts/mails/Welcome.vue' )

        ], 'inertia-mailable-vue-ts' );
    }
}
