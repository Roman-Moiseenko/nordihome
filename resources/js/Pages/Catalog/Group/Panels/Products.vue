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
                :route="route('admin.catalog.group.products.attach', {id: groupId})"
            />
            <SearchAddProducts
                :route="route('admin.catalog.group.products.attach', {id: groupId})"
                class="ml-3"/>
        </div>
        <TableRelation entity="group" :id="groupId" />

    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {route} from "ziggy-js";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import TableRelation from "@Comp/Product/TableRelation.vue";

const props = defineProps({
    groupId: Number,
})

</script>
