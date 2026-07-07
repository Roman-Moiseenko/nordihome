<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>

        <el-table
            :data="[...tableData]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="image" label="IMG" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="code" label="Артикул" width="160"/>
            <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="priceSell" label="Цена продажи" width="120"/>
            <el-table-column prop="priceBase" label="Цена базовая" width="120"/>
            <el-table-column prop="productId" label="Есть в каталоге" width="120">
                <template #default="scope">

                    <Active :active="scope.row.productId !== null"/>
                </template>
            </el-table-column>
            <el-table-column prop="availability" label="Доступен" width="120">
                <template #default="scope">
                    <Active :active="scope.row.availability"/>
                </template>
            </el-table-column>
            <el-table-column label="Действия">
                <template #default="scope">
                    <el-button :type="scope.row.availability ? 'info' : 'success'" size="small"
                               @click.stop="onAvailable(scope.row)">
                        {{ scope.row.availability ? 'Недоступен' : 'Доступен' }}
                    </el-button>
                    <el-button :type="scope.row.fragile ? 'success' : 'warning'" size="small"
                               @click.stop="onFragile(scope.row)">
                        {{ scope.row.fragile ? 'Не Хрупкий' : 'Хрупкий' }}
                    </el-button>
                    <el-button :type="scope.row.sanctioned ? 'success' : 'danger'" size="small"
                               @click.stop="onSanctioned(scope.row)">
                        {{ scope.row.sanctioned ? 'Не Санкционный' : 'Санкционный' }}
                    </el-button>

                    <el-button type="success" size="small" @click.stop="onParser(scope.row)">
                        Спарсить
                    </el-button>

                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-center mt-4" v-if="pagination.last_page > 1">
            <el-pagination
                :current-page="pagination.current_page"
                :page-size="pagination.per_page"
                :total="pagination.total"
                layout="prev, pager, next"
                @current-change="onPageChange"
            />
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps, onMounted, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {route} from "ziggy-js";
import api from "@Res/api";

const props = defineProps({
    categoryId: Number,
})

interface Pagination {
    current_page: number
    last_page: number
    per_page: number
    total: number
    data: Product[]
}

interface Product {
    id: number
    code: string
    name: string
    image: string | null
    available: boolean
    fragile: boolean
    sanctioned: boolean,
    productId: number | null
}

const tableData = ref<Product[]>([])
const loading = ref(false)
const pagination = ref<Pagination>({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    data: [],
})
const currentPage = ref(1);
onMounted(() => {
    fetchProducts(1)
})
function fetchProducts(page: number = 1) {
    currentPage.value = page
    loading.value = true
    axios.get(route(`admin.parser.category.products`, {id: props.categoryId, page}))
        .then(response => {
            pagination.value = response.data
            tableData.value = response.data.data

            // Загружаем изображения для полученных товаров
            const ids = response.data.data.map((p: Product) => p.id)
            if (ids.length > 0) {
                axios.get(route('admin.photo.get-by-ids'), {
                    params: {
                        imageableIds: ids,
                        modelType: 'parser.product',
                        type: 'gallery',
                    }
                }).then(photoResponse => {
                    tableData.value = tableData.value.map((product: Product) => ({
                        ...product,
                        image: photoResponse.data[product.id] || null,
                    }))
                })
            }
        })
        .finally(() => {
            loading.value = false
        })
}

function onPageChange(page: number) {
    fetchProducts(page)
}


function routeClick(row: any) {
    if (row.productId !== null) router.get(route('admin.catalog.product.edit', {id: row.productId}))
}

function onParser(row) {
    api.post(route('admin.parser.product.parser', {id: row.id}))
}

function onAvailable(row) {
    router.visit(route('admin.parser.product.available', {id: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            fetchProducts(currentPage.value)
        }
    })
}

function onFragile(row) {
    router.visit(route('admin.parser.product.fragile', {id: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            fetchProducts(currentPage.value)
        }
    })
}

function onSanctioned(row) {
    router.visit(route('admin.parser.product.sanctioned', {id: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            fetchProducts(currentPage.value)
        }
    })
}

</script>
