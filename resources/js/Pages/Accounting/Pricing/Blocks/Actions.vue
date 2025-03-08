<template>
    <template v-if="pricing.completed">

        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.pricing.work', {pricing: pricing.id})" />
    </template>
    <template v-else-if="!pricing.trashed">
        <SearchAddProduct
            :route="route('admin.accounting.pricing.add-product', {pricing: pricing.id})"
            :quantity="false"
        />
        <SearchAddProducts :route="route('admin.accounting.pricing.add-products', {pricing: pricing.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.pricing.completed', {pricing: pricing.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.pricing.restore', {pricing: pricing.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <AccountingFilter />
    <DeleteEntityModal name_entity="Ценообразование" name="document"/>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import {inject} from "vue";

const props = defineProps({
    pricing: Object,
})
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.pricing.full-destroy', {pricing: props.pricing.id}), {name: "document"});
}
</script>
