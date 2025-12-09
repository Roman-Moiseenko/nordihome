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
                    <el-button :type="scope.row.availability ? 'info' : 'success'" size="small" @click.stop="onAvailable(scope.row)">
                        {{ scope.row.availability ? 'Недоступен' :  'Доступен'}}
                    </el-button>
                    <el-button :type="scope.row.fragile ? 'success' : 'warning'" size="small" @click.stop="onFragile(scope.row)">
                        {{ scope.row.fragile ? 'Не Хрупкий' : 'Хрупкий' }}
                    </el-button>
                    <el-button :type="scope.row.sanctioned ? 'success' : 'danger'" size="small" @click.stop="onSanctioned(scope.row)">
                        {{ scope.row.sanctioned ? 'Не Санкционный' : 'Санкционный' }}
                    </el-button>

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
    router.visit(route('admin.parser.product.parser', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

function onAvailable(row) {
    router.visit(route('admin.parser.product.available', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

function onFragile(row) {
    router.visit(route('admin.parser.product.fragile', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

function onSanctioned(row) {
    router.visit(route('admin.parser.product.sanctioned', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

</script>
