<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Товары Икеа</h1>
        <div class="flex">

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Товар"/>
                <el-select v-model="filter.category" placeholder="Выберите категорию" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                </el-select>
                <el-select v-model="filter.show" placeholder="Показать" class="mt-1">
                    <el-option key="availability" value="availability" label="Только доступные"/>
                    <el-option key="not_availability" value="not_sale" label="Недоступные"/>
                    <el-option key="fragile" value="draft" label="Хрупкие"/>
                    <el-option key="sanctioned" value="delete" label="Санкционные"/>
                </el-select>
            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                v-loading="store.getLoading"
                row-key="id"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="code" label="Артикул" width="120"/>
                <el-table-column prop="name" label="Название" show-overflow-tooltip>
                    <template #default="scope">
                        <div>
                            {{ scope.row.name }}
                        </div>
                        <div>
                            <div class="show-on-hover items-center">
                                <el-link :type="scope.row.availability ? 'info' : 'success'" :underline="false"
                                         @click="onAvailable(scope.row)">
                                    {{ scope.row.availability ? 'Недоступен' : 'Доступен' }}
                                </el-link>
                                &nbsp;|&nbsp;
                                <el-link :type="scope.row.fragile ? 'success' : 'warning'" :underline="false"
                                         @click="onFragile(scope.row)">
                                    {{ scope.row.fragile ? 'Не Хрупкий' : 'Хрупкий' }}
                                </el-link>
                                &nbsp;|&nbsp;
                                <el-link :type="scope.row.sanctioned ? 'success' : 'danger'" :underline="false"
                                         @click="onSanctioned(scope.row)">
                                    {{ scope.row.sanctioned ? 'Не Санкционный' : 'Санкционный' }}
                                </el-link>
                                &nbsp;|&nbsp;
                                <el-link type="primary" :underline="false"
                                         @click="onProduct(scope.row)">
                                    К товару
                                </el-link>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="category_name" label="Категория" width="250" align="center"
                                 show-overflow-tooltip/>
                <el-table-column prop="availability" label="Доступен" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.availability"/>
                    </template>
                </el-table-column>

                <el-table-column prop="fragile" label="Хрупкий" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.fragile"/>
                    </template>
                </el-table-column>
                <el-table-column prop="sanctioned" label="Санкционный" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.sanctioned"/>
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
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import {Head, Link, router} from "@inertiajs/vue3";
import Pagination from "@Comp/Pagination.vue";
import {defineProps, reactive, ref} from "vue";
import {route} from "ziggy-js";
import TableFilter from "@Comp/TableFilter.vue";
import Active from "@Comp/Elements/Active.vue";
import SelectActions from "@Page/Product/Product/SelectActions.vue";


const props = defineProps({
    products: Object,
    title: {
        type: String,
        default: 'Список всех товаров Икеа',
    },
    filters: Array,
    categories: Array,
    count: Array,
})
const tableData = ref([...props.products.data])
const filter = reactive({
    name: props.filters.name,
    category: props.filters.category,
    show: props.filters.show,
})
const store = useStore();

function onAvailable(row) {
    router.visit(route('admin.parser.product.available', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableData.value = [...page.props.products.data]
        }
    })
}

function onFragile(row) {
    router.visit(route('admin.parser.product.fragile', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableData.value = [...page.props.products.data]
        }
    })
}

function onSanctioned(row) {
    router.visit(route('admin.parser.product.sanctioned', {product_parser: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableData.value = [...page.props.products.data]
        }
    })
}
function onProduct(row) {
    router.get(route('admin.product.edit', {product: row.product_id}))
}
</script>
