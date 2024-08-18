<script setup lang="ts">

import Layout from '/vendor/capsulescodes/inertia-mailable/components/vue/Layout.vue';
import Table from '/vendor/capsulescodes/inertia-mailable/components/vue/tags/Table.vue';


interface WelcomeProps
{
    name : string;
}


const props = defineProps<WelcomeProps>();


const app = import.meta.env.VITE_APP_NAME;

</script>

<template>

    <Layout>

        <template v-slot:default>

            <p>Hello, {{ props.name }}!</p>

            <p>This is a mail made with Laravel, Inertia, Vue and Typescript</p>

            <Table align="center">

                <Table align="center">

                    <Table>

                        <img class="my-4 max-w-full" src="https://capsules.codes/storage/canvas/images/LentWCgPB1iFQgUsSfBf3NgNznNH4FwFaAD0XecL.png">

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
