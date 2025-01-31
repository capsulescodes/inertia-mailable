<script setup>

import Table from './tags/Table.vue';


const name = import.meta.env.VITE_APP_NAME;
const date = new Date().getFullYear();

</script>

<template>

    <Table class="p-10" align="center" v-bind:width="'570'">

        <p class="text-xs text-center text-slate-400" v-text="`© ${date} ${name}. All rights reserved`" />

    </Table>

</template>
