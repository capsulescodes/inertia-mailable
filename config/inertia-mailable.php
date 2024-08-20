<?php

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
    | Manifest  path
    |--------------------------------------------------------------------------
    |
    | The path where the manifest is located.
    |
    */

    'manifest' => public_path( 'build/manifest.json' ),

    /*
    |--------------------------------------------------------------------------
    | Built resources path
    |--------------------------------------------------------------------------
    |
    | The path where the built resources are located.
    |
    */

    'build' => public_path( 'build' ),

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

    'css' => resource_path( 'css/mail.css' )
];
