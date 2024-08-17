<script setup lang="ts">

import Layout from '~/vue/Layout.vue';


interface WelcomeProps
{
    name : string;
}


const props = defineProps<WelcomeProps>();

</script>

<template>

    <Layout>

        <template v-slot:default>

            <p>Hello, {{ props.name }}!</p>

            <p>This is a mail from Capsules Codes made with Laravel, Inertia, Vue and Typescript</p>

            <p>Regards,</p>

            <p>Capsules Codes</p>

        </template>

        <template v-slot:subcopy>

            <p class="text-xs">This is a subcopy from Capsules Codes made with Laravel, Inertia and Vue</p>

        </template>

    </Layout>

</template>
