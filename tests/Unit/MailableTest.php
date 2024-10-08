<?php

use CapsulesCodes\InertiaMailable\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Response;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Response as HttpResponse;


beforeEach( function() : void
{
    $this->mailable = new Mailable();

    $this->reflection = new ReflectionClass( $this->mailable );
} );



it( "can return default root view if root is empty", function() : void
{
    $method = $this->reflection->getMethod( 'getRoot' );

    expect( $method->invoke( $this->mailable ) )->toBe( 'inertia-mailable::mail' );
} );


it( "can modify root view", function() : void
{
    $method = $this->reflection->getMethod( 'getRoot' );

    $this->mailable->root = 'foo';

    expect( $method->invoke( $this->mailable ) )->toBe( 'foo' );

    $this->mailable->root( 'bar' );

    expect( $method->invoke( $this->mailable ) )->toBe( 'bar' );

    $this->mailable->root = '';

    expect( $method->invoke( $this->mailable ) )->toBe( 'inertia-mailable::mail' );
} );


it( "can add props", function() : void
{
    $method = $this->reflection->getMethod( 'getData' );

    expect( $method->invoke( $this->mailable )[ 'props' ] )->toBeEmpty();

    $this->mailable->propsData = [ 'foo' => 'bar' ];

    expect( $method->invoke( $this->mailable )[ 'props' ] )->toBe( [ 'foo' => 'bar' ] );

    $this->mailable->view( '', [ 'baz' => 'qux' ] );

    expect( $method->invoke( $this->mailable )[ 'props' ] )->toBe( [ 'foo' => 'bar', 'baz' => 'qux' ] );

    $this->mailable->propsData = [];

    expect( $method->invoke( $this->mailable )[ 'props' ] )->toBe( [] );
} );


it( "can render a mail as html", function() : void
{
    $mailable = Mockery::mock( Mailable::class )->shouldAllowMockingProtectedMethods()->makePartial();

    $mailable->shouldReceive( 'prepare' )
        ->shouldReceive( 'getHtml' )->andReturn( '<div class="foo">Hello World</div>' )
        ->shouldReceive( 'getCss' )->andReturn( '.foo{display:block;}' );

    expect( $mailable->render() )->toContain( '<div style="display: block;">Hello World</div>' );
} );


it( "can return default data", function() : void
{
    $method = $this->reflection->getMethod( 'getData' );

    $default = [ 'component' => null, 'props' => [], 'rootView' => 'inertia-mailable::mail', 'viewData' => [] ];

    expect( $method->invoke( $this->mailable ) )->toBe( $default );
} );

it( "can return custom data", function() : void
{
    $method = $this->reflection->getMethod( 'getData' );

    $this->mailable->root( 'foo' )->view( 'Bar' );

    $this->mailable->propsData = [ 'baz' => 'qux' ];

    $this->mailable->viewData = [ 'corge' => 'grault' ];

    $custom = [ 'component' => 'Bar', 'props' => [ 'baz' => 'qux' ], 'rootView' => 'foo', 'viewData' => [ 'corge' => 'grault' ] ];

    expect( $method->invoke( $this->mailable ) )->toBe( $custom );
} );


it( 'throws an exception when the component file does not exist', function() : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    expect( fn() => $method->invoke( $this->mailable, [ 'component' => null ] ) )->tothrow( Exception::class, "Component [] not found." );
} );


it( 'throws an exception when the file is not found', function() : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    Config::set( 'inertia-mailable.inertia', 'foo' );

    $file = Config::get( 'inertia-mailable.inertia' );

    expect( fn() => $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->tothrow( Exception::class, "File not found at path : {$file}. Please run 'npm run build', publish file or modify config entries." );
} );


it( 'throws an exception when node is not found', function() : void
{
    $method = $this->reflection->getMethod( 'process' );

    Config::set( 'inertia-mailable.node', 'foo' );

    expect( fn() => $method->invoke( $this->mailable, [ '-v' ] ) )->toThrow( Exception::class );
} );


it( 'converts Html and Css successfully', function() : void
{
    $method = $this->reflection->getMethod( 'convert' );

    $html = "<div class=\"foo\">Hello World</div>";

    $css = '.foo { color: red; }';

    expect( $method->invoke( $this->mailable, $html, $css ) )->toContain( "<div style=\"color: red;\">Hello World</div>" );
} );


it( 'returns the expected output when parsing Inertia components', function () : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    Config::set( 'inertia-mailable.inertia', 'tests/Fixtures/bootstrap/ssr/vue-js.js' );

    App::shouldReceive( 'basePath' )->with( Config::get( 'inertia-mailable.inertia' ) )->andReturn( Config::get( 'inertia-mailable.inertia' ) );

    Process::shouldReceive( 'run' )->andReturnSelf()->shouldReceive( 'failed' )->andReturnFalse()->shouldReceive( 'output' )->andReturn( '<div>Hello World</div>' );

    expect( $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->toBe( '<div>Hello World</div>' );
} );


it( 'generates the expected HTML', function() : void
{
    $data = [ 'component' => 'Baz', 'props' => [], 'rootView' => 'foo.bar', 'viewData' => [] ];

    $blade = '<html>  <body>  <div id="inertia">  </div>  </body>  </html>';

    $inertia = json_encode([ 'body' => '<div>Hello World</div>' ]);

    $id = 'inertia';


    $mock = Mockery::mock( Mailable::class )->makePartial();

    $mock->root( $data[ 'rootView' ] )->view( $data[ 'component' ], [] );

    $mock->shouldAllowMockingProtectedMethods()->shouldReceive( 'getData' )->andReturn( $data );

    $response = Mockery::mock( HttpResponse::class )->shouldReceive( 'getContent' )->andReturn( $blade )->getMock();

    Response::shouldReceive( 'view' )->with( $data[ 'rootView' ], [ 'page' => $data ] )->andReturn( $response );

    $mock->shouldAllowMockingProtectedMethods()->shouldReceive( 'getInertia' )->with( $data )->andReturn( $inertia );

    Config::set( 'inertia-mailable.id', $id );

    $crawler = new Crawler( $blade );

    $output = Str::replace( $crawler->filter( "#$id" )->first()->outerHtml(), json_decode( $inertia, true )[ 'body' ], $crawler->first()->outerHtml() );

    $html = preg_replace('/>\s+</', '><', html_entity_decode( $output ) );

    expect( $mock->getHtml() )->toBe( $html );

} );


it( 'returns null when neither CSS file nor Tailwind exists', function()
{
    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( 'resources/css/mail.css' ) )->andReturn( false )
        ->shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBeNull();
} );


it( 'returns CSS when the CSS file exists', function()
{
    $css = '.body { color: red; }';

    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( $css )
        ->shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBe( $css );
} );


it( 'compiles Tailwind CSS when Tailwind exists', function ()
{
    $css = '.body { color: blue; }';

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( $css )
        ->shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( true )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.tailwind' ) ) )->andReturn( false );

    $disk = Mockery::mock();

    $disk->shouldReceive( 'exists' )->andReturnTrue()
        ->shouldReceive( 'put' )->andReturnTrue()
        ->shouldReceive( 'path' )
        ->shouldReceive( 'has' )->andReturnFalse();

    Storage::shouldReceive( 'build' )->andReturn( $disk );

    $mock = Mockery::mock( Mailable::class )->makePartial();

    Process::shouldReceive( 'path' )->andReturnSelf()
        ->shouldReceive( 'run' )->andReturnSelf()
        ->shouldReceive( 'failed' )->andReturnFalse()
        ->shouldReceive( 'output' )->andReturn( $css );

    expect( $mock->getCss() )->toBe( $css );
} );


it( "compiles tailwind CSS correctly without comments", function()
{
    $css = "/* comment */ .body { color: green; }";

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( true )
        ->shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false )
        ->shouldReceive( 'get' )->with( App::basePath( 'resources/css/mail.css' ) )->andReturn( $css );

    $result = Mockery::mock( Mailable::class )->makePartial()->getCss();

    expect( $result )->not->toContain( "/* comment */" );
    expect( $result )->toContain( ".body { color: green; }" );
} );


it( "compiles HTML and CSS correctly without classes", function()
{
    $css = '.my-4 { margin-top: 10rem; margin-bottom: 10rem; } .max-w-full { max-width: 100%; }';

    $html = '<div class="my-4 max-w-full" >Hello World</div>';

    $method = $this->reflection->getMethod( 'convert' );

    expect( $method->invoke( $this->mailable, $html, $css ) )->toContain( "<div style=\"margin-top: 10rem; margin-bottom: 10rem; max-width: 100%;\">Hello World</div" );
} );
