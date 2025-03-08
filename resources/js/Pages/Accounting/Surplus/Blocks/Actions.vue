<template>
    <template v-if="surplus.completed">
        <AccountingWork v-if="!surplus.inventory"  :route="route('admin.accounting.surplus.work', {surplus: props.surplus.id})" />
    </template>
    <template v-else-if="!surplus.trashed">
        <SearchAddProduct
            :route="route('admin.accounting.surplus.add-product', {surplus: surplus.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.surplus.add-products', {surplus: surplus.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.surplus.completed', {surplus: props.surplus.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.surplus.restore', {surplus: surplus.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <AccountingPrint />
    <AccountingFilter />
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(surplus.amount) }}</el-tag>
    </span>
    <DeleteEntityModal name_entity="Оприходование" name="document"/>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps, inject} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";

const props = defineProps({
    surplus: Object,
})
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.surplus.full-destroy', {surplus: props.surplus.id}), {name: "document"});
}

</script>
