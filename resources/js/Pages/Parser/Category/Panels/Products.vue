<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>

        <el-table
            :data="[...category.products]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="code" label="Артикул"  width="120"/>
            <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
        </el-table>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    category: Object,
})
function routeClick(row) {
    //TODO
    // router.get(route('admin.parser.product.show', {product: row.id}))
}
</script>
