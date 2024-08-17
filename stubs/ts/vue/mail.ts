import { createInertiaApp } from '@inertiajs/vue3';
import * as process from 'process';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { DefineComponent, createSSRApp, h } from 'vue';


createInertiaApp( {

    page : JSON.parse( process.argv[ 2 ] ),
    render : renderToString,
    resolve : ( name : string ) => resolvePageComponent( `./mails/${name}.vue`, import.meta.glob<Promise<DefineComponent>>( './mails/**/*.vue', { eager : true } ) ).then( page => page ),
    setup( { App, props, plugin } ){ return createSSRApp( { render : () => h( App, props ) } ).use( plugin ); }

} ).then( data => process.stdout.write( JSON.stringify( data ) ) );
