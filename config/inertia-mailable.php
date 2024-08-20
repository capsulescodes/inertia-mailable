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
    | Server Side Rendering built resources path
    |--------------------------------------------------------------------------
    |
    | The path where the manifest is located.
    |
    */

    'ssr' => base_path( 'bootstrap/ssr' ),

    /*
    |--------------------------------------------------------------------------
    | Inertia Javascript or Typescript filename
    |--------------------------------------------------------------------------
    |
    | The name of the Inertia resource. Default is main.js.
    |
    */

    'file' => 'mail.js',


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
