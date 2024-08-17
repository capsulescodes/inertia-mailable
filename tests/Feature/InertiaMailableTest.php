<?php

use CapsulesCodes\InertiaMailable\Mail\Mailable;
use CapsulesCodes\InertiaMailable\Tests\App\Mail;


beforeEach( function() : void
{
    $this->email = new Mail( 'foo@bar.baz', 'Qux' );
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
    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/App/build' ) );

    Config::set( 'inertia-mailable.js', 'corge' );

    expect( fn() => $this->email->render() )->toThrow( Exception::class, "File not found in manifest. Please run 'npm run build' or publish the preferred file." );
} );


it( "can render a mail with custom CSS", function()
{
    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/App/build' ) );

    Config::set( 'inertia-mailable.js', 'tests/App/resources/js/vue/mail.js' );

    Config::set( 'inertia-mailable.css', 'tests/App/resources/css/plain.css' );

    expect( $this->email->render() )->toContain( "<div class=\"custom-css\" style=\"background-color: rgb( 12 34 56 );\"></div>" );
} );


it( "can render a mail with Tailwind CSS", function()
{
    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/App/build' ) );

    Config::set( 'inertia-mailable.js', 'tests/App/resources/js/vue/mail.js' );

    Config::set( 'inertia-mailable.css', 'tests/App/resources/css/tailwind.css' );

    App::shouldReceive( 'basePath' )->with()->andReturn( getcwd() );

    App::shouldReceive( 'basePath' )->with( 'node_modules/.bin/tailwind' )->andReturn( 'node_modules/.bin/tailwind' );

    expect( $this->email->render() )->toContain( "<div class=\"block tailwind-css\" style=\"--tw-bg-opacity: 1; background-color: rgb(153 246 228 / var(--tw-bg-opacity)); display: block;\"></div>" );
} );




it( "can render a mail based on Vue Javascript file", function() : void
{
    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/App/build' ) );

    Config::set( 'inertia-mailable.js', 'tests/App/resources/js/vue/mail.js' );

    expect( $this->email->render() )
        ->toContain( "<p>Hello, Qux!</p>" )
        ->toContain( "<p>This is a mail from Capsules Codes made with Laravel, Inertia, Vue and Javascript</p>" )
        ->toContain( "<p>Regards,</p>" )
        ->toContain( "<p>Capsules Codes</p>" );
} );


it( "can render a mail based on Vue Typescript file", function() : void
{
    Config::set( 'inertia-mailable.build', realpath( dirname( __DIR__ ) . '/App/build' ) );

    Config::set( 'inertia-mailable.ts', 'tests/App/resources/ts/vue/mail.ts' );

    expect( $this->email->render() )
        ->toContain( "<p>Hello, Qux!</p>" )
        ->toContain( "<p>This is a mail from Capsules Codes made with Laravel, Inertia, Vue and Typescript</p>" )
        ->toContain( "<p>Regards,</p>" )
        ->toContain( "<p>Capsules Codes</p>" );
} );
