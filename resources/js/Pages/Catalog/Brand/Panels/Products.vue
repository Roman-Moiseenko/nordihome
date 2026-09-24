<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>
        <div class="p-5 bg-white rounded-md mt-5">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" width="300" show-overflow-tooltip/>
                <el-table-column prop="category" label="Основная категория" width="" show-overflow-tooltip />
            </el-table>
            <Pagination
                :current_page="brand.products.current_page"
                :per_page="brand.products.per_page"
                :total="brand.products.total"
            />
        </div>
    </el-tab-pane>
</template>

<script lang="ts" setup>
import {ref, defineProps} from "vue";
import {router} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import Pagination from "@Comp/Pagination.vue";

const props = defineProps({
    brand: Object,
})

const store = useStore();
const tableData = ref([...props.brand.products.data])

function routeClick(row) {
    router.get(route('admin.catalog.product.show', {product: row.id}))
}

</script>
