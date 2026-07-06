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

</script>
