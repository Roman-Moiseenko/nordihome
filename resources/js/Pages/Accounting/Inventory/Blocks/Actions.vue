<template>
    <template v-if="inventory.completed">
        <AccountingWork :route="route('admin.accounting.inventory.work', {inventory: inventory.id})" />
    </template>
    <template v-else-if="!inventory.trashed">
        <SearchAddProduct
            :route="route('admin.accounting.inventory.add-product', {inventory: inventory.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.inventory.add-products', {inventory: inventory.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.inventory.completed', {inventory: inventory.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.inventory.restore', {inventory: inventory.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <AccountingPrint />
    <AccountingFilter />
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(inventory.amount_formal ) }}</el-tag>
    </span>
    <span class="ml-2">
        Сумма факт. <el-tag type="success" size="large">{{ func.price(inventory.amount_actually ) }}</el-tag>
    </span>
    <DeleteEntityModal name_entity="Инвентаризацию" name="document"/>
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
    inventory: Object,
})
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.inventory.full-destroy', {inventory: props.inventory.id}), {name: "document"});
}
</script>
