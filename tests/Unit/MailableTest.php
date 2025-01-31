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
        ->shouldReceive( 'getHtml' )->andReturn( '<div class="foo">Foo</div>' )
        ->shouldReceive( 'getCss' )->andReturn( '.foo{display:block;}' );

    expect( $mailable->render() )->toContain( '<div style="display: block;">Foo</div>' );
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

    expect( fn() => $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->tothrow( Exception::class, "File not found at path : '{$file}'. Please run 'npm run build', publish file or modify config entries." );
} );


it( 'throws an exception when node is not found', function() : void
{
    $method = $this->reflection->getMethod( 'process' );

    Config::set( 'inertia-mailable.node', 'foo' );

    expect( fn() => $method->invoke( $this->mailable, [ '-v' ] ) )->toThrow( Exception::class, "exec: foo: not found" );
} );


it( 'returns the expected output when parsing Inertia component', function () : void
{
    $inertia = '<div>Foo</div>';

    $method = $this->reflection->getMethod( 'getInertia' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.inertia' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.inertia' ) ) )->andReturn( $inertia )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( false );

    Process::shouldReceive( 'run' )->andReturnSelf()->shouldReceive( 'failed' )->andReturnFalse()->shouldReceive( 'output' )->andReturn( $inertia );

    expect( $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->toBe( $inertia );
} );


it( 'returns the expected output when parsing Inertia component located in manifest', function () : void
{
    $content = [ Config::get( 'inertia-mailable.inertia' ) => [ "file" => Config::get( 'inertia-mailable.inertia' ) ] ];

    $directory = dirname( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) );

    $path = Arr::get( Arr::get( $content, Config::get( 'inertia-mailable.inertia' ) ), 'file' );

    $inertia = '<div>Foo</div>';

    $method = $this->reflection->getMethod( 'getInertia' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.inertia' ) ) )->andReturn( false )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( json_encode( $content ) )
        ->shouldReceive( 'exists' )->with( "$directory/$path" )->andReturn( true );

    Process::shouldReceive( 'run' )->andReturnSelf()->shouldReceive( 'failed' )->andReturnFalse()->shouldReceive( 'output' )->andReturn( $inertia );

    expect( $method->invoke( $this->mailable, [ 'component' => 'Foo' ] ) )->toBe( $inertia );
} );


it( 'generates the expected HTML', function() : void
{
    $data = [ 'component' => 'Baz', 'props' => [], 'rootView' => 'foo.bar', 'viewData' => [] ];

    $blade = '<html>  <body>  <div id="inertia">  </div>  </body>  </html>';

    $inertia = json_encode([ 'body' => '<div>Foo</div>' ]);

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

    expect( $mock->getHtml() )->toBe( html_entity_decode( $output ) );

} );


it( 'returns null when CSS file does not exist', function()
{
    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( false )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBeNull();
} );


it( 'returns CSS when the CSS file exists', function()
{
    $css = '.body { color: red; }';

    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( $css )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBe( $css );
} );


it( 'returns Tailwind CSS when Tailwind CSS file exists', function ()
{
    $css = '.body { color: blue; }';

    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( $css )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( false );

    expect( $method->invoke( $this->mailable, [] ) )->toBe( $css );
} );


it( 'returns Tailwind CSS when Tailwind CSS file exists in manifest', function ()
{
    $content = [ Config::get( 'inertia-mailable.css' ) => [ "file" => Config::get( 'inertia-mailable.css' ) ] ];

    $directory = dirname( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) );

    $path = Arr::get( Arr::get( $content, Config::get( 'inertia-mailable.css' ) ), 'file' );

    $css = '.body { color: blue; }';

    $method = $this->reflection->getMethod( 'getCss' );

    File::shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.css' ) ) )->andReturn( false )
        ->shouldReceive( 'exists' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( true )
        ->shouldReceive( 'get' )->with( App::basePath( Config::get( 'inertia-mailable.manifest' ) ) )->andReturn( json_encode( $content ) )
        ->shouldReceive( 'exists' )->with( "$directory/$path" )->andReturn( true )
        ->shouldReceive( 'get' )->with( "$directory/$path" )->andReturn( $css );

    expect( $method->invoke( $this->mailable, [] ) )->toBe( $css );
} );


it( 'converts Html and CSS correctly', function() : void
{
    $html = '<div class="foo">Foo</div>';

    $css = '.foo { color: red; }';

    $method = $this->reflection->getMethod( 'convert' );

    expect( $method->invoke( $this->mailable, $html, $css ) )->toContain( '<div style="color: red;">Foo</div>' );
} );


it( "compiles HTML correctly without extra space between tags", function()
{
    $html = '<div>Foo</div>    <div>Bar</div>';

    $method = $this->reflection->getMethod( 'convert' );

    expect( $method->invoke( $this->mailable, $html, null ) )->toContain( '<div>Foo</div><div>Bar</div>' );
} );


it( "compiles HTML and CSS correctly without HTML comments", function()
{
    $html = '<!--foo--><div>Foo</div>';

    $method = $this->reflection->getMethod( 'convert' );

    expect( $method->invoke( $this->mailable, $html, null ) )->toContain( '<div>Foo</div' );
} );


it( "compiles HTML and CSS correctly without classes", function()
{
    $css = '.my-4 { margin-top: 10rem; margin-bottom: 10rem; } .max-w-full { max-width: 100%; }';

    $html = '<div class="my-4 max-w-full" >Foo</div>';

    $method = $this->reflection->getMethod( 'convert' );

    expect( $method->invoke( $this->mailable, $html, $css ) )->toContain( '<div style="margin-top: 10rem; margin-bottom: 10rem; max-width: 100%;">Foo</div' );
} );
