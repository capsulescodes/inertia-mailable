import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';


export default defineConfig( {
    plugins : [ vue(), manifest() ],
    resolve : { alias : { '/vendor/capsulescodes/inertia-mailable/components' : '/components' } },
    build : {
        outDir : 'tests/Fixtures/bootstrap/ssr',
        emptyOutDir : true,
        manifest : true,
        rollupOptions : {
            input : {
                'vue-js' : 'stubs/js/vue/mail.js',
                'vue-ts' : 'stubs/ts/vue/mail.ts'
            },
            output : {
                entryFileNames : '[name].js'
            }
        }
    }
} );


function manifest()
{
    const path = 'tests/Fixtures/bootstrap/ssr';

    return {
        apply : 'build',
        writeBundle()
        {
            fs.renameSync( `${path}/.vite/manifest.json`, `${path}/manifest.json` );
            fs.rmdirSync( `${path}/.vite` );
        }
    };
}
