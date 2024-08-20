<script setup lang="ts">

import Table from './tags/Table.vue';


const name = import.meta.env.VITE_APP_NAME;
const logotype = 'https://github.com/capsulescodes/inertia-mailable/blob/bee1fee37803a93b9cb35d4e778f5471b8c7455a/art/capsules-inertia-mailable-logotype-background.png';

</script>

<template>

    <Table class="p-12 text-center">

        <img v-bind:src="logotype" class="h-12 max-h-12 w-12 mb-3" v-bind:alt="name">

        <a v-bind:href="name" class="block text-lg font-bold no-underline text-primary-black" v-text="name" />

    </Table>

</template>
