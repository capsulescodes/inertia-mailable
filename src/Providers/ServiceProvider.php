<?php

namespace CapsulesCodes\InertiaMailable\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\App;


class ServiceProvider extends BaseServiceProvider
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

            "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ),
            "$stubsPath/js/react/mail.jsx" => App::resourcePath( 'js/mail.jsx' ),
            "$stubsPath/js/react/mails/Welcome.jsx" => App::resourcePath( 'js/mails/Welcome.jsx' )

        ], 'inertia-mailable-react-js' );


        $this->publishes( [

            "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ),
            "$stubsPath/ts/react/mail.tsx" => App::resourcePath( 'ts/mail.tsx' ),
            "$stubsPath/ts/react/mails/Welcome.tsx" => App::resourcePath( 'ts/mails/Welcome.tsx' )

        ], 'inertia-mailable-react-ts' );


        $this->publishes( [

            "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ),
            "$stubsPath/js/vue/mail.js" => App::resourcePath( 'js/mail.js' ),
            "$stubsPath/js/vue/mails/Welcome.vue" => App::resourcePath( 'js/mails/Welcome.vue' )

        ], 'inertia-mailable-vue-js' );


        $this->publishes( [

            "$stubsPath/css/mail.css" => App::resourcePath( 'css/mail.css' ),
            "$stubsPath/ts/vue/mail.ts" => App::resourcePath( 'ts/mail.ts' ),
            "$stubsPath/ts/vue/mails/Welcome.vue" => App::resourcePath( 'ts/mails/Welcome.vue' )

        ], 'inertia-mailable-vue-ts' );
    }
}
