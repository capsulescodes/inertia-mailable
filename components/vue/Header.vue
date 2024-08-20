<script setup lang="ts">

import Table from './tags/Table.vue';


const name = import.meta.env.VITE_APP_NAME;
const logotype = 'https://github.com/capsulescodes/inertia-mailable/blob/e9fdf63a970db6ef52a6eec02fcfd0dbcd80fd48/capsules-inertia-mailable-logotype-background.png';

</script>

<template>

    <Table class="p-12 text-center">

        <img v-bind:src="logotype" class="h-12 max-h-12 w-12 mb-3" v-bind:alt="name">

        <a v-bind:href="name" class="block text-lg font-bold no-underline text-primary-black" v-text="name" />

    </Table>

</template>
