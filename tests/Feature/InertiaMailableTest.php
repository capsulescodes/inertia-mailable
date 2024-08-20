<?php

use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Tests\Fixtures\Mail;


beforeEach( function() : void
{
    $this->email = new Mail( 'Qux' );
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


it( "returns an exception if manifest is not found", function() : void
{
    Config::set( 'inertia-mailable.build', "quux" );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "Vite manifest not found" );
} );


it( "returns an exception if file not found in manifest", function() : void
{
    Config::set( 'inertia-mailable.manifest', realpath( dirname( __DIR__ ) . '/Fixtures/public/build/manifest.json' ) );

    Config::set( 'inertia-mailable.js', 'corge' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "File not found in manifest. Please run 'npm run build' or publish the preferred file." );
} );


it( "can render a mail with Tailwind CSS", function()
{
    $path = realpath( dirname( __DIR__ ) . '/Fixtures/public/build' );

    Config::set( 'inertia-mailable.manifest', "$path/manifest.json" );

    Config::set( 'inertia-mailable.js', 'stubs/js/vue/mail.js' );

    Config::set( 'inertia-mailable.build', $path );

    App::shouldReceive( 'basePath' )->with()->andReturn( getcwd() );

    App::shouldReceive( 'basePath' )->with( 'node_modules/.bin/tailwind' )->andReturn( 'node_modules/.bin/tailwind' );

    expect( $this->email->render() )->toContain( "<img class=\"my-4 max-w-full\" src=\"https://raw.githubusercontent.com/capsulescodes/inertia-mailable/main/art/capsules-inertia-mailable-mail-image.png\" style=\"margin-top: 1rem; margin-bottom: 1rem; max-width: 100%;\">" );
} );


it( "can render a mail in server side rendering", function() : void
{
    Config::set( 'inertia-mailable.manifest', realpath( dirname( __DIR__ ) . '/Fixtures/public/build/manifest.json' ) );

    Config::set( 'inertia-mailable.js', 'stubs/js/vue/mail.js' );

    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/Fixtures/bootstrap/ssr' ) );

    expect( $this->email->render() )->toContain( "<p>Hello, Qux!</p>" )->toContain( "<p>This is a mail made with Laravel, Inertia, Vue and Javascript</p>" );
} );



it( "can render a mail based on Vue Javascript file", function() : void
{
    $path = realpath( dirname( __DIR__ ) . '/Fixtures/public/build' );

    Config::set( 'inertia-mailable.manifest', "$path/manifest.json" );

    Config::set( 'inertia-mailable.js', 'stubs/js/vue/mail.js' );

    Config::set( 'inertia-mailable.build', $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Qux!</p>" )->toContain( "<p>This is a mail made with Laravel, Inertia, Vue and Javascript</p>" );
} );


it( "can render a mail based on Vue Typescript file", function() : void
{
    $path = realpath( dirname( __DIR__ ) . '/Fixtures/public/build' );

    Config::set( 'inertia-mailable.manifest', "$path/manifest.json" );

    Config::set( 'inertia-mailable.ts', 'stubs/ts/vue/mail.ts' );

    Config::set( 'inertia-mailable.build', $path );

    expect( $this->email->render() )->toContain( "<p>Hello, Qux!</p>" )->toContain( "<p>This is a mail made with Laravel, Inertia, Vue and Typescript</p>" );
} );
