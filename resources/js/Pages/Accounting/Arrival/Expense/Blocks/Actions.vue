<template>
    <template v-if="expense.completed">
        <AccountingOnBased :based="expense.based" :founded="expense.founded" />
        <AccountingPrint />
    </template>
    <template v-else>
        <el-form class="flex">
            <el-input v-model="form.name" placeholder="Номенклатура" style="width: 260px"/>
            <el-input v-model="form.quantity" style="width: 120px" class="ml-2">
                <template #append>шт</template>
            </el-input>
            <el-input v-model="form.cost" style="width: 160px" class="ml-2">
                <template #append>{{ expense.currency_sign }}</template>
            </el-input>
            <el-button type="success" plain class="ml-5" @click="onSubmit">Добавить</el-button>
        </el-form>

    </template>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(expense.amount) }}</el-tag>
    </span>

</template>

<script setup>
import {defineProps, reactive} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    expense: Object,
})

const form = reactive({
    name: null,
    quantity: 1,
    cost: null,
})

function onSubmit() {
    if (form.name === null || form.quantity === null || form.cost === null) return;
    console.log(form)
    router.visit(route('admin.accounting.arrival.expense.add-item', {expense: props.expense.id}), {
        method: "post",
        onPreserveState: true,
        data: form,
        onSuccess: page => {
            form.name = null
            form.quantity = 1
            form.cost = null
        }
    })
}

</script>
