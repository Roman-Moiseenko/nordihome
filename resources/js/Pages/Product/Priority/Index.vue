<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Товары приоритетного показа</h1>
        <div class="flex mt-5">

            <SearchAddProduct
                :route="route('admin.product.priority.add-product')"
            />
            <SearchAddProducts :route="route('admin.product.priority.add-products')" class="ml-3"/>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">

                <el-select filterable v-model="filter.category" placeholder="Категория" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :label="item.name"
                               :value="item.id"/>
                </el-select>
                <el-input v-model="filter.product" placeholder="Товар" class="mt-1"/>
            </TableFilter>
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" width="300" show-overflow-tooltip/>
                <el-table-column prop="category" label="Основная категория" width="" show-overflow-tooltip />

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из приоритетного показа" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'

const props = defineProps({
    products: Object,
    title: {
        type: String,
        default: 'Товары приоритетного показа',
    },
    filters: Array,
    categories: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.products.data])
const filter = reactive({
    product: props.filters.product,
    category: props.filters.category,
})
interface IRow {
    quantity: number
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.quantity === 0) {
        return 'warning-row'
    }
    return ''
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.priority.del-product', {product: row.id}));
}
function routeClick(row) {
    router.get(route('admin.product.edit', {product: row.id}))
}
</script>

