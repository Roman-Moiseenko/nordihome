<template>
    <template v-if="expense.completed">
        <AccountingPrint />
    </template>
    <template v-else-if="!expense.trashed">
        <el-form class="flex">
            <el-input v-model="form.name" placeholder="Номенклатура" style="width: 260px"/>
            <el-input v-model="form.quantity" style="width: 120px" class="ml-2">
                <template #append>шт</template>
            </el-input>
            <el-input v-model="form.cost" style="width: 160px" class="ml-2">
                <template #append>{{ expense.currency_sign }}</template>
            </el-input>
            <el-button type="primary" class="ml-5" @click="onSubmit">Добавить</el-button>
            <el-button type="danger" plain class="ml-auto" @click="onDelete">На удаление</el-button>
        </el-form>
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.arrival.expense.restore', {expense: expense.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(expense.amount) }}</el-tag>
    </span>
    <DeleteEntityModal name_entity="Доп.расходы" name="document" />
</template>

<script setup>
import {inject, reactive} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";

const props = defineProps({
    expense: Object,
})
const form = reactive({
    name: null,
    quantity: 1,
    cost: null,
})
const $delete_entity = inject("$delete_entity")

function onSubmit() {
    if (form.name === null || form.quantity === null || form.cost === null) return;

    router.visit(route('admin.accounting.arrival.expense.add-item', {expense: props.expense.id}), {
        method: "post",
        onPreserveState: true,
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            form.name = null
            form.quantity = 1
            form.cost = null
        }
    })
}
function onDelete() {
    $delete_entity.show(
        route('admin.accounting.arrival.expense.destroy', {expense: props.expense.id}),
        {name: "document", soft: true}
    );
}
function onForceDelete() {
    $delete_entity.show(
        route('admin.accounting.arrival.expense.full-destroy', {expense: props.expense.id}),
        {name: "document"}
    );
}
</script>
