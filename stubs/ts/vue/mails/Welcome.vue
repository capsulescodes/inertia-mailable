<script setup lang="ts">

import Layout from '/vendor/capsulescodes/inertia-mailable/components/ts/vue/Layout.vue';
import Table from '/vendor/capsulescodes/inertia-mailable/components/ts/vue/tags/Table.vue';


interface WelcomeProps
{
    name : string;
}


const props = defineProps<WelcomeProps>();


const app = import.meta.env.VITE_APP_NAME;
const image = 'https://raw.githubusercontent.com/capsulescodes/inertia-mailable/main/art/capsules-inertia-mailable-mail-image.png';

</script>

<template>

    <Layout>

        <template v-slot:default>

            <p class="text-slate-800">Hello, {{ props.name }}!</p>

            <p class="pt-4 text-sm text-slate-600">This is a mail made with Laravel, Inertia and Vue with Typescript</p>

            <Table align="center">

                <Table align="center">

                    <Table>

                        <img class="my-4 max-w-full" v-bind:src="image">

                    </Table>

                </Table>

            </Table>

            <p class="pb-4 text-sm text-slate-600">Regards,</p>

            <p class="text-slate-800">{{ app }}</p>

        </template>

        <template v-slot:subcopy>

            <p class="text-xs text-slate-600">This is a subcopy made with Laravel, Inertia and Vue with Typescript</p>

        </template>

    </Layout>

</template>
