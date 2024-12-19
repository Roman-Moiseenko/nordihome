<template>
    <TableFilter :filter="filter" class="ml-2 " :count="filter.product ? 1 : undefined" :show_close="false">
        <el-input v-model="filter.product" placeholder="Товар" class="mt-1"/>
    </TableFilter>
</template>

<script setup lang="ts">
import TableFilter from "@Comp/TableFilter.vue";
import {inject, reactive} from "vue";

const $filters = inject('$filters', [])
const filter = reactive({
    product: $filters.product,
})

</script>
<style scoped>

</style>
