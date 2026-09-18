<template>
    <Head><title>Остатки товаров</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Остатки товаров</h1>

        <div class="flex">

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.code" placeholder="Артикул"/>
                <el-select v-model="filter.categoryId" placeholder="Выберите категорию" class="mt-1" clearable filterable>
                    <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id" :label="item.name"/>
                </el-select>
            </TableFilter>
        </div>
        <div class="bg-white rounded-lg my-2 p-1 shadow flex">
            <SearchAddProduct
                :route="route('admin.accounting.stock.add-product')"
                :quantity="true" caption="Изменить"
            />
            <SearchAddProducts :route="route('admin.accounting.stock.add-products')" class="ml-3"/>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%"
                v-loading="store.getLoading"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="category" label="Категория" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Кол-во" width="120" align="center"/>
                <el-table-column prop="reserve" label="Резерв" width="120" align="center"/>
            </el-table>
        </div>

        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head} from "@inertiajs/vue3";
import {defineProps, reactive, ref} from "vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";

const useCatalog = useCatalogStore()

const props = defineProps({
    products: Object,
    filters: Object,
})

const store = useStore();
const tableData = ref([...props.products.data])

const filter = reactive({
    code: props.filters.code,
    categoryId: props.filters.categoryId,
})
</script>

<style scoped>

</style>
