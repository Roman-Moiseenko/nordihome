<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-tree"></i>
                <span> Изменилась цена</span>
            </span>
        </template>

        <el-table
            :data="[...items]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"

        >
            <el-table-column prop="code" label="Артикул" width="100" />
            <el-table-column prop="category" label="Категория товара" width="300" />
            <el-table-column prop="category_parser" label="Категории парсера"  width="280" show-overflow-tooltip/>
            <el-table-column prop="price_old" label="Старая цена" width="100" />
            <el-table-column prop="price_new" label="Новая цена" width="100" />
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

const props = defineProps({
    items: Array,
})
console.log(props.items)
function routeClick(row) {
    router.get(route('admin.product.show', {product: row.product_id}))
}
</script>
