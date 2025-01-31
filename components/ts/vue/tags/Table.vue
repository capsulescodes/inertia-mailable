<script setup lang="ts">

interface TableProps
{
    width ? : string;
    cellpadding ? : string;
    cellspacing ? : string;
    role ? : string;
}


const props = defineProps<TableProps>();

</script>

<template>

    <table
        v-bind:width="props.width ?? '100%'"
        v-bind:cellpadding="props.cellpadding ?? '0'"
        v-bind:cellspacing="props.cellspacing ?? '0'"
        v-bind:role="props.role ?? 'presentation'"
    >

        <tr>

            <td>

                <slot />

            </td>

        </tr>

    </table>

</template>
