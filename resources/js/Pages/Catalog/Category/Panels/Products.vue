<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-box-open"></i>
                <span> Товары</span>
            </span>
        </template>
        <div class="flex mt-5">

            <SearchAddProduct
                :route="route('admin.catalog.category.products.attach', {id: categoryId})"
            />
            <SearchAddProducts
                :route="route('admin.catalog.category.products.attach', {id: categoryId})"
                class="ml-3"/>

        </div>
        <TableRelation entity="category" :id="categoryId" />


    </el-tab-pane>
    <DeleteEntityModal name_entity="Товар" name="product"/>
</template>

<script setup lang="ts">
import {defineProps, inject, onMounted, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import axios from 'axios';
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import TableFilter from "@Comp/TableFilter.vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import TableRelation from "@Comp/Product/TableRelation.vue";

const props = defineProps({
    categoryId: Number,
})

interface Product {
    id: number
    code: string
    name: string
    image: string | null
    published: boolean
    not_sale: boolean
}

interface Pagination {
    current_page: number
    last_page: number
    per_page: number
    total: number
    data: Product[]
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

const $delete_entity = inject("$delete_entity", "product")

onMounted(() => {
    fetchProducts(1)
})

function fetchProducts(page: number = 1) {
    loading.value = true
    axios.get(route('admin.catalog.category.products', {id: props.categoryId, page}))
        .then(response => {
            pagination.value = response.data
            tableData.value = response.data.data
        })
        .finally(() => {
            loading.value = false
        })
}

function onPageChange(page: number) {
    fetchProducts(page)
}

function routeClick(row) {
    router.get(route('admin.catalog.product.edit', {product: row.id}))
}

function onRestore(row) {
    router.post(route('admin.catalog.product.restore', {product: row.id}))
}

function onFullDelete(row) {
    $delete_entity.show(route('admin.catalog.product.full-delete', {id: row.id}));
}

function onEdit(row) {
    router.visit(route('admin.catalog.product.edit', {product: row.id}), {
        method: "get",
        preserveState: true,
        preserveScroll: true,
    })
}

function onAnalitics(row) {
    router.get(route('admin.catalog.product.show', {product: row.id}))
}

function onCreateProduct() {
    router.get(route('admin.catalog.product.create'))
}

function onSaleToggle(row) {
    router.visit(route('admin.catalog.product.sale', {product: row.id}), {
        method: "post",
        preserveState: false,
        preserveScroll: true,
    })
}

function onPublishedToggle(row) {
    router.visit(route('admin.catalog.product.toggle', {product: row.id}), {
        method: "post",
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            fetchProducts(pagination.value.current_page)
        }
    })
}

</script>
