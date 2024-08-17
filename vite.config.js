import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';


export default defineConfig( {
    plugins : [ vue(), manifest() ],
    resolve : { alias : { '~' : '/components' } },
    build : {
        target : 'esnext',
        outDir : 'tests/App/build',
        emptyOutDir : true,
        manifest : true,
        rollupOptions : {
            input : {
                'vue-js' : 'tests/App/resources/js/vue/mail.js',
                'vue-ts' : 'tests/App/resources/ts/vue/mail.ts'
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
            const path = 'tests/App/build';
            fs.renameSync( `${path}/.vite/manifest.json`, `${path}/manifest.json` );
            fs.rmdirSync( `${path}/.vite` );
        }
    };
}
