<script setup>

import Layout from '~/vue/Layout.vue';


const props = defineProps( { name : { type : String, required : true } } );

</script>

<template>

    <Layout>

        <template v-slot:default>

            <p>Hello, {{ props.name }}!</p>

            <p>This is a mail from Capsules Codes made with Laravel, Inertia, Vue and Javascript</p>

            <p>Regards,</p>

            <p>Capsules Codes</p>

            <div class="custom-css" />

            <div class="block tailwind-css" />

        </template>

        <template v-slot:subcopy>

            <p class="text-xs">This is a subcopy from Capsules Codes made with Laravel, Inertia and Vue</p>

        </template>

    </Layout>

</template>
