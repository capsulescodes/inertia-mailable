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


it( "can render mail as html", function() : void
{
    $mailable = Mockery::mock( Mailable::class )->shouldAllowMockingProtectedMethods()->makePartial();

    $mailable->shouldReceive( 'prepare' );

    $mailable->shouldReceive( 'getHtml' )->andReturn( '<div class="foo">Hello World</div>' );

    $mailable->shouldReceive( 'getCss' )->andReturn( '.foo{display:block;}' );

    expect( $mailable->render() )->toContain( '<div class="foo" style="display: block;">Hello World</div>' );
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


it( 'throws an exception when the manifest file does not exist', function() : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.build' )->andReturn( 'build' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.build' )->andReturn( 'build' );

    $path = "build/manifest.json";

    File::shouldReceive( 'exists' )->with( $path )->andReturn( false );

    expect( fn() => $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->tothrow( Exception::class, "Vite manifest not found." );
} );


it( 'throws an exception when the file is not found in the manifest', function() : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.build' )->andReturn( 'build' );

    $path = "build/manifest.json";

    File::shouldReceive( 'exists' )->with( $path )->andReturn( true );

    File::shouldReceive( 'get' )->with( $path )->andReturn( json_encode( [] ) );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.js' )->andReturn( 'app.js' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.ts' )->andReturn( null );

    expect( fn() => $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->tothrow( Exception::class, "File not found in manifest. Please run 'npm run build' or publish the preferred file." );
} );


it( 'returns the expected output when parsing Inertia components', function () : void
{
    $method = $this->reflection->getMethod( 'getInertia' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.build' )->andReturn( 'build' );

    $path = "build/manifest.json";

    File::shouldReceive( 'exists' )->with( $path )->andReturn( true );

    $mock = json_encode([ 'foo.bar' => [ 'file' => 'baz.qux' ] ] );

    File::shouldReceive( 'get' )->with( $path )->andReturn( $mock );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.js' )->andReturn( 'foo.bar' );

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.ts' )->andReturn( null );

    Process::shouldReceive( 'path' )->with( App::basePath() )->andReturnSelf();

    Process::shouldReceive( 'run' )->andReturnSelf();

    Process::shouldReceive( 'output' )->andReturn( '<div>Hello World</div>' );

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

    Config::partialMock()->shouldReceive( 'get' )->with( 'inertia-mailable.inertia' )->andReturn( $id );

    $crawler = new Crawler( $blade );

    $output = Str::replace( $crawler->filter( "#$id" )->first()->outerHtml(), json_decode( $inertia, true )[ 'body' ], $crawler->first()->outerHtml() );

    $html = preg_replace('/>\s+</', '><', html_entity_decode( $output ) );

    expect( $mock->getHtml() )->toBe( $html );

} );


it( 'returns null when neither CSS file nor Tailwind exists', function()
{
    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( 'resources/css/mail.css' ) )->andReturn( false );

    File::shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBeNull();
} );


it( 'returns CSS when the CSS file exists', function()
{
    $css = '.body { color: red; }';

    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( Config::get( 'inertia-mailable.css' ) )->andReturn( true );

    File::shouldReceive( 'get' )->with( Config::get( 'inertia-mailable.css' ) )->andReturn( $css );

    File::shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBe( $css );
} );


it( 'compiles Tailwind CSS when Tailwind exists', function ()
{
    $css = '.body { color: blue; }';

    $data = [ 'component' => 'Baz', 'props' => [], 'rootView' => 'foo.bar', 'viewData' => [] ];

    $inertia = json_encode([ 'body' => '<div>Hello World</div>' ]);

    File::shouldReceive( 'exists' )->with( Config::get( 'inertia-mailable.css' ) )->andReturn( true );

    File::shouldReceive( 'get' )->with( Config::get( 'inertia-mailable.css' ) )->andReturn( $css );

    File::shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( true );

    $mock = Mockery::mock( Mailable::class )->makePartial();

    $mock->root( $data[ 'rootView' ] )->view( $data[ 'component' ], [] );

    $mock->shouldAllowMockingProtectedMethods()->shouldReceive( 'getData' )->andReturn( $data );

    $mock->shouldAllowMockingProtectedMethods()->shouldReceive( 'getInertia' )->with( $data )->andReturn( $inertia );

    Storage::shouldReceive( 'put' )->once();

    Storage::shouldReceive( 'path' )->andReturn( '/fake/path/to/temp/file' );

    Process::shouldReceive( 'path' )->andReturnSelf();

    Process::shouldReceive( 'run' )->andReturnSelf();

    Process::shouldReceive( 'output' )->andReturn( $css );

    Storage::shouldReceive( 'has' )->once()->andReturn( true );

    Storage::shouldReceive( 'delete' )->once();

    expect( $mock->getCss() )->toBe( $css );
} );


it( "compiles tailwind CSS correctly without comments", function()
{
    $css = "/* comment */ .body { color: green; }";

    File::shouldReceive( 'exists' )->with( Config::get( 'inertia-mailable.css' ) )->andReturn( true );

    File::shouldReceive( 'exists' )->with( App::basePath( 'node_modules/.bin/tailwind' ) )->andReturn( false );

    File::shouldReceive( 'get' )->with( App::basePath( 'resources/css/mail.css' ) )->andReturn( $css );

    $mock = Mockery::mock( Mailable::class )->makePartial();

    $result = $mock->getCss();

    expect( $result )->not->toContain( "/* comment */" );

    expect( $result )->toContain( ".body { color: green; }" );
} );
