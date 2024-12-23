<template>
    <template v-if="refund.completed">
        <AccountingWork :route="route('admin.accounting.refund.work', {refund: props.refund.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.refund.add-product', {refund: refund.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.refund.add-products', {refund: refund.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.refund.completed', {refund: props.refund.id})" />
    </template>
    <AccountingOnBased />
    <AccountingPrint />
    <AccountingFilter />
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
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue";

const props = defineProps({
    refund: Object,
})


</script>
