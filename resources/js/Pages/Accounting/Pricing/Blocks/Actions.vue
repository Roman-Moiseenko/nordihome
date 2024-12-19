<template>
    <template v-if="pricing.completed">

        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.pricing.work', {pricing: props.pricing.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.pricing.add-product', {pricing: pricing.id})"
            :quantity="false"
        />
        <SearchAddProducts :route="route('admin.accounting.pricing.add-products', {pricing: pricing.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.pricing.completed', {pricing: props.pricing.id})" />
    </template>
    <AccountingOnBased />
    <AccountingFilter />
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
    pricing: Object,
})

</script>
