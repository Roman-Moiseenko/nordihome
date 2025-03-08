<template>
    <template v-if="departure.completed">
        <AccountingPrint />
        <AccountingWork v-if="!departure.inventory"  :route="route('admin.accounting.departure.work', {departure: props.departure.id})" />
    </template>
    <template v-else-if="!departure.trashed">
        <SearchAddProduct
            :route="route('admin.accounting.departure.add-product', {departure: departure.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.departure.add-products', {departure: departure.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.departure.completed', {departure: props.departure.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.departure.restore', {departure: departure.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <AccountingPrint />
    <AccountingFilter />
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(departure.amount) }}</el-tag>
    </span>
    <DeleteEntityModal name_entity="Списание" name="document"/>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import {inject} from "vue";

const props = defineProps({
    departure: Object,
})
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.departure.full-destroy', {departure: props.departure.id}), {name: "document"});
}

</script>
