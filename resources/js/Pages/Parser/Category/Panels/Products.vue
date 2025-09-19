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
            <el-table-column type="expand">
                <template #default="scope">
                    {{ scope.row.data }}
                </template>
            </el-table-column>
            <el-table-column prop="code" label="Артикул"  width="160"/>
            <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="price_sell" label="Цена продажи" width="120"/>
            <el-table-column prop="price_base" label="Цена базовая" width="120"/>
            <el-table-column prop="price_base" label="Доступен" width="120">
                <template #default="scope">
                    <Active :active="scope.row.availability" />
                </template>
            </el-table-column>
            <el-table-column label="Действия">
                <template #default="scope">
                    Недоступен, Хрупкий, Санкционный
                    <el-button type="success" size="small" @click.stop="onParser(scope.row)">
                        Спарсить
                    </el-button>
                </template>
            </el-table-column>
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
    router.get(route('admin.product.edit', {product: row.product_id}))
}
function onParser(row) {
    router.visit(route('admin.parser.product.parser', {product: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

</script>
