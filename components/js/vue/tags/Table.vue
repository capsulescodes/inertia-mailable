<script setup>

const props = defineProps( { width : String, cellpadding : String, cellspacing : String, role : String } );

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
