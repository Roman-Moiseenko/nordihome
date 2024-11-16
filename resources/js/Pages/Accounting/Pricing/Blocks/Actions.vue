<template>
    <template v-if="pricing.completed">
        <AccountingOnBased :based="pricing.based" />
        <AccountingPrint :print="pricing.print" />
        <el-button type="danger" class="ml-5" @click="onWork">Отменить проведение</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.pricing.add-product', {pricing: pricing.id})"
            :quantity="false"
        />
        <SearchAddProducts :route="route('admin.accounting.pricing.add-products', {pricing: pricing.id})" class="ml-3"/>
        <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
    </template>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBase from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    pricing: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.pricing.completed', {pricing: props.pricing.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.pricing.work', {pricing: props.pricing.id}), {
        method: "post",
    })
}

</script>
