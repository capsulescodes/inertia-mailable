<script setup lang="ts">

import Table from './tags/Table.vue';


const name = import.meta.env.VITE_APP_NAME;
const logotype = "https://capsules.codes/build/assets/capsules-logotype-background-BSi258Cg.svg";

</script>

<template>

<Table class="p-12 text-center">

    <img v-bind:src="logotype" class="h-12 max-h-12 w-12 mb-3" v-bind:alt="name">

    <a v-bind:href="name" class="block text-lg font-bold no-underline text-primary-black" v-text="name" />

</Table>

</template>
