<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-tree"></i>
                <span> Новые товары</span>
            </span>
        </template>

        <el-table
            :data="[...items]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"

        >
            <el-table-column prop="code" label="Артикул" width="100" />
            <el-table-column prop="productId" label="Связанный товар" width="300" >
                <template #default="scope">
                    <Active :active="scope.row.productId !== null" />
                </template>
            </el-table-column>
            <el-table-column prop="categoryParser" label="Категории парсера" show-overflow-tooltip>
                <template #default="scope">
                    <el-tag v-for="category in scope.row.categoryParser">{{ category }}</el-tag>
                </template>
            </el-table-column>

            <el-table-column label="Действия" align="right">
                <template #default="scope">

                </template>
            </el-table-column>
        </el-table>

        <!--ParserItem v-for="item in items" :item="item" /-->

    </el-tab-pane>

</template>
<script setup lang="ts">
import {inject, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import {route} from "ziggy-js";

const props = defineProps({
    items: Array,
})
console.log(props.items)
function routeClick(row) {
    if (row.productId !== null) router.get(route('admin.catalog.product.edit', {id: row.productId}))
}
</script>
