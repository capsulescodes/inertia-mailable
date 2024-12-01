<?php

use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Tests\Fixtures\App\Mail\Base;


beforeEach( function() : void
{
    $this->email = new Base( 'Qux' );
} );


it( "returns an exception if component doesn't exist", function() : void
{
    $this->email = new Mailable();

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "Component [] not found." );
} );


it( "returns an exception if root view doesn't exist", function() : void
{
    $this->email->root( 'foo' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "View [foo] not found." );
} );


it( "returns an exception if root view is not found", function() : void
{
    $this->email->root( 'Quux' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "View [Quux] not found." );
} );


it( "returns an exception if file is not found", function() : void
{
    Config::set( 'inertia-mailable.file', 'corge' );

    $file = Config::get( 'inertia-mailable.inertia' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "File not found at path : {$file}. Please run 'npm run build', publish file or modify config entries." );
} );


it( "can render a mail with CSS", function()
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/custom.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( function( $path ){ if( $path === 'node_modules/.bin/tailwind' ) return ''; return $path; } );

    expect( $this->email->render() )->toContain( "<p style=\"font-size: 3px; line-height: 4px;\">© 2024 undefined. All rights reserved</p>" );
} );


it( "can render a mail with Tailwind CSS", function()
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'stubs/css/mail.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p style=\"text-align: center; font-size: 0.75rem; line-height: 1rem; --tw-text-opacity: 1; color: rgb(148 163 184 / var(--tw-text-opacity, 1));\">© 2024 undefined. All rights reserved</p>" );
} );


it( "can render a mail with Tailwind CSS and a custom config file", function()
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'stubs/css/mail.css' );

    Config::set( 'inertia-mailable.tailwind', 'tests/Fixtures/tailwind.config.js' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p style=\"text-align: center; font-size: 6px; line-height: 8px; --tw-text-opacity: 1; color: rgb(148 163 184 / var(--tw-text-opacity, 1));\">© 2024 undefined. All rights reserved</p>" );
} );


it( "can render a mail based on Vue Javascript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'stubs/css/mail.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Qux!</p>" )->toContain( "<p>This is a mail made with Laravel, Inertia and Vue</p>" );
} );


it( "can render a mail based on Vue Typescript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-ts.js' );

    Config::set( 'inertia-mailable.css', 'stubs/css/mail.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Qux!</p>" )->toContain( "<p>This is a mail made with Laravel, Inertia and Vue with Typescript</p>" );
} );
