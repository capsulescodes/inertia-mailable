<?php

use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Tests\Fixtures\App\Mail\Base;


beforeEach( function() : void
{
    $this->email = new Base( 'Foo' );
} );


it( "returns an exception if component doesn't exist", function() : void
{
    $this->email = new Mailable();

    expect( fn() => $this->email->render() )->toThrow( Exception::class, 'Component [] not found.' );
} );


it( "returns an exception if root view doesn't exist", function() : void
{
    $this->email->root( 'foo' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, 'View [foo] not found.' );
} );


it( "returns an exception if root view is not found", function() : void
{
    $this->email->root( 'Quux' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, 'View [Quux] not found.' );
} );


it( "returns an exception if file is not found", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'corge' );

    $file = Config::get( 'inertia-mailable.inertia' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "File not found at path : '{$file}'. Please run 'npm run build', publish file or modify config entries." );
} );


it( "can render a mail with CSS", function()
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/plain.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( '<p style="font-size: 3px; line-height: 4px;">' );
} );


it( "can render a mail with Tailwind CSS in public directory", function()
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    Config::set( 'inertia-mailable.manifest', 'tests/Fixtures/public/build/manifest.json' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( '<p style="text-align: center; font-size: var(--text-xs); line-height: var(--tw-leading,var(--text-xs--line-height)); color: var(--color-slate-400);">' );
} );


it( "can render a mail with Tailwind CSS located in server side directory", function()
{
    Config::set( 'inertia-mailable.inertia', 'stubs/js/vue/mail.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    Config::set( 'inertia-mailable.manifest', 'tests/Fixtures/bootstrap/ssr/manifest.json' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( '<p style="text-align: center; font-size: var(--text-xs); line-height: var(--tw-leading,var(--text-xs--line-height)); color: var(--color-slate-400);">' );
} );


it( "can render a mail based on Vue Javascript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Foo!</p>" )->toContain( '<p>This is a mail made with Laravel, Inertia and Vue</p>' );
} );


it( "can render a mail based on Vue Typescript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-ts.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Foo!</p>" )->toContain( '<p>This is a mail made with Laravel, Inertia and Vue with Typescript</p>' );
} );


it( "can render a mail based on React Javascript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/react-js.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Foo!</p>" )->toContain( '<p>This is a mail made with Laravel, Inertia and React</p>' );
} );


it( "can render a mail based on React Typescript file", function() : void
{
    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/react-ts.js' );

    Config::set( 'inertia-mailable.css', 'tests/Fixtures/resources/css/tailwind.css' );

    App::shouldReceive( 'basePath' )->andReturnUsing( fn( $path ) => $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Foo!</p>" )->toContain( '<p>This is a mail made with Laravel, Inertia and React with Typescript</p>' );
} );
