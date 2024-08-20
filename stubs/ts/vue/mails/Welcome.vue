<script setup lang="ts">

import Layout from '/vendor/capsulescodes/inertia-mailable/components/vue/Layout.vue';
import Table from '/vendor/capsulescodes/inertia-mailable/components/vue/tags/Table.vue';


interface WelcomeProps
{
    name : string;
}


const props = defineProps<WelcomeProps>();


const app = import.meta.env.VITE_APP_NAME;
const image = 'https://github.com/capsulescodes/inertia-mailable/blob/e9fdf63a970db6ef52a6eec02fcfd0dbcd80fd48/capsules-inertia-mailable-logotype-background.png';

</script>

<template>

    <Layout>

        <template v-slot:default>

            <p>Hello, {{ props.name }}!</p>

            <p>This is a mail made with Laravel, Inertia, Vue and Typescript</p>

            <Table align="center">

                <Table align="center">

                    <Table>

                        <img class="my-4 max-w-full" v-bind:src="image">

                    </Table>

                </Table>

            </Table>

            <p>Regards,</p>

            <p>{{ app }}</p>

        </template>

        <template v-slot:subcopy>

            <p class="text-xs">This is a subcopy made with Laravel, Inertia and Vue</p>

        </template>

    </Layout>

</template>
