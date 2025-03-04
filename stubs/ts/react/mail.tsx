import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { renderToString } from 'react-dom/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';


createInertiaApp( {

    page : JSON.parse( process.argv[ 2 ] ),
    render : renderToString,
    resolve : ( name : string ) => resolvePageComponent( `./mails/${name}.tsx`, import.meta.glob<Promise<React.ComponentType>>( './mails/**/*.tsx', { eager : true } ) ).then( page => page ) ,
    setup : ( { App, props } ) => <App { ...props } />

} ).then( data => process.stdout.write( JSON.stringify( data ) ) );
