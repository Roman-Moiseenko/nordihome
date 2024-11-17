<template>
    <template v-if="inventory.completed">
        <AccountingOnBased :based="inventory.based" :founded="inventory.founded"/>
        <AccountingPrint />
        <el-button type="danger" class="ml-5" @click="onWork">Отменить проведение</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.inventory.add-product', {inventory: inventory.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.inventory.add-products', {inventory: inventory.id})" class="ml-3"/>
        <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
    </template>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(inventory.amount_formal ) }}</el-tag>
    </span>
    <span class="ml-2">
        Сумма факт. <el-tag type="success" size="large">{{ func.price(inventory.amount_actually ) }}</el-tag>
    </span>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    inventory: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.inventory.completed', {inventory: props.inventory.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.inventory.work', {inventory: props.inventory.id}), {
        method: "post",
    })
}

</script>
