<template>
    <template v-if="refund.completed">
        <AccountingOnBased :based="refund.based" />
        <AccountingPrint :print="refund.print" />
        <el-button type="danger" class="ml-5" @click="onWork">Отмена проведения</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.refund.add-product', {refund: refund.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.refund.add-products', {refund: refund.id})" class="ml-3"/>
        <el-button type="danger" class="ml-5" @click="onCompleted" plain>Провести документ</el-button>
    </template>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(refund.amount, refund.currency) }}</el-tag>
    </span>
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
    refund: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.refund.completed', {refund: props.refund.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.refund.work', {refund: props.refund.id}), {
        method: "post",
    })
}

</script>
