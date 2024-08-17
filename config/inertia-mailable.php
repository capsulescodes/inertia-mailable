<?php

use Illuminate\Support\Facades\App;


return [

    /*
    |--------------------------------------------------------------------------
    | Inertia identifier
    |--------------------------------------------------------------------------
    |
    | The div identifier used by Inertia.
    |
    */

    'inertia' => 'app',

    /*
    |--------------------------------------------------------------------------
    | Build path
    |--------------------------------------------------------------------------
    |
    | The path where the built resources are located.
    |
    */

    'build' => App::publicPath( 'build' ),

    /*
    |--------------------------------------------------------------------------
    | Javascript filename
    |--------------------------------------------------------------------------
    |
    | The path where the Javascript resource is located in the manifest.
    |
    */

    'js' => 'resources/js/mail.js',

    /*
    |--------------------------------------------------------------------------
    | Typescript filename
    |--------------------------------------------------------------------------
    |
    | The path where the Typescript resource is located in the manifest.
    |
    */

    'ts' => 'resources/ts/mail.ts',

        /*
    |--------------------------------------------------------------------------
    | CSS filename
    |--------------------------------------------------------------------------
    |
    | The path where the CSS resource is located.
    |
    */

    'css' => App::resourcePath( 'css/mail.css' )
];
