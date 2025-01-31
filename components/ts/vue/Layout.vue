<script setup lang="ts">

import { computed, useSlots } from 'vue';
import Table from './tags/Table.vue';
import Header from './Header.vue';
import Footer from './Footer.vue';


interface LayoutProps
{
    logotype ? : string;
}


const props = defineProps<LayoutProps>();


const hasSubCopy = computed( () => !! useSlots()[ 'subcopy' ] );

</script>

<template>

    <Table class="bg-slate-100" align="center">

        <Header v-bind:logotype="props.logotype" />

        <Table class="p-8 drop-shadow-md bg-white" align="center" width="570">

            <slot />

            <Table v-if="hasSubCopy" class="mt-6 pt-6 border-0 border-t border-solid border-slate-200">

                <slot name="subcopy" />

            </Table>

        </Table>

        <Footer />

    </Table>

</template>
