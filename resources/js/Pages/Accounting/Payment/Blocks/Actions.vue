<template>
    <template v-if="payment.completed">
        <AccountingPrint />
        <AccountingWork :route="route('admin.accounting.payment.work', {payment: props.payment.id})" />
    </template>
    <template v-else-if="!payment.trashed">
        <el-button type="success" @click="onAdd">Добавить неоплаченные</el-button>
        <AccountingCompleted :route="route('admin.accounting.payment.completed', {payment: props.payment.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.payment.restore', {payment: payment.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <DeleteEntityModal name_entity="Платежный документ" name="document"/>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import {inject} from "vue";

const props = defineProps({
    payment: Object,
})
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.payment.full-destroy', {payment: props.payment.id}), {name: "document"});
}
function onAdd() {
    router.post(route('admin.accounting.payment.not-paid', {payment: props.payment.id}))
}
</script>
