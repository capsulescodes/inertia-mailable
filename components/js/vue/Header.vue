<script setup>

import Table from './tags/Table.vue';


const props = defineProps( { logotype : String } );


const name = import.meta.env.VITE_APP_NAME;
const logotype = props.logotype ?? 'https://raw.githubusercontent.com/capsulescodes/inertia-mailable/main/art/capsules-inertia-mailable-logotype.png';

</script>

<template>

    <Table class="p-12 text-center">

        <img v-bind:src="logotype" class="w-12 max-h-12 mb-3" v-bind:alt="name">

        <a v-bind:href="name" class="block text-lg font-bold no-underline text-slate-600" v-text="name" />

    </Table>

</template>
