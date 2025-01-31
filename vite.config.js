import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';


export default defineConfig( ( { isSsrBuild } ) => ( {
    plugins : [ tailwindcss(), vue() ],
    resolve : { alias : { '/vendor/capsulescodes/inertia-mailable/components' : '/components' } },
    build : {
        outDir : isSsrBuild ? 'tests/Fixtures/bootstrap/ssr' : 'tests/Fixtures/public/build',
        emptyOutDir : true,
        manifest : 'manifest.json',
        ssrEmitAssets : true,
        rollupOptions :  {
            input : isSsrBuild ? { 'vue-js' : 'stubs/js/vue/mail.js', 'vue-ts' : 'stubs/ts/vue/mail.ts', 'mail-css' : 'tests/Fixtures/resources/css/tailwind.css' } : { 'mail-css' : 'tests/Fixtures/resources/css/tailwind.css' }
        }
    }
} ) );
