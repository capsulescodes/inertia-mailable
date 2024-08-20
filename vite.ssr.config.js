import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';


export default defineConfig( {
    plugins : [ vue(), manifest() ],
    resolve : { alias : { '/vendor/capsulescodes/inertia-mailable/components' : '/components' } },
    build : {
        target : 'esnext',
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
        },
        ssr : true
    }
} );


function manifest()
{
    return {
        apply : 'build',
        writeBundle()
        {
            const path = 'tests/Fixtures/bootstrap/ssr';
            fs.renameSync( `${path}/.vite/manifest.json`, `${path}/manifest.json` );
            fs.rmdirSync( `${path}/.vite` );
        }
    };
}
