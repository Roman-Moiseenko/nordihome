<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <el-form-item label="№ документа">
                <el-input v-model="info.number" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" style="width: 160px"/>
                <span class="text-gray-500 px-4">от</span>
                <el-date-picker v-model="info.created_at" type="datetime"
                                @change="setInfo" :disabled="iSavingInfo"
                                style="width: 200px"
                                :readonly="notEdit"
                />
            </el-form-item>
            <el-form-item label="Плательщик">
                <el-select v-model="info.trader_id" @change="setInfo" :disabled="iSavingInfo || notEdit" style="width: 260px">
                    <el-option v-for="item in traders" :key="item.id" :value="item.id" :label="item.shot_name" :readonly="notEdit">{{ item.full_name }}</el-option>
                </el-select>
                <span class="ml-2 mr-1">Счет</span>
                <span class="text-blue-900">{{ payment.account }}</span>

            </el-form-item>
            <el-form-item label="Сумма оплаты">
                <el-input v-model="info.amount" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" style="width: 160px"/>
                <span class="ml-2 mr-1">Долг</span>
                <span class="text-blue-900">{{ func.price(payment.debit, payment.currency) }}</span>
            </el-form-item>
            <el-form-item label="Статья расходов">
                <el-input :model-value="payment.supply_id ? 'Оплата заказа' : 'Оплата долга'" :readonly="true" style="width: 300px"/>
                <Link v-if="payment.supply_id" type="primary" class="ml-2"
                         :href="route('admin.accounting.supply.show', {supply: payment.supply_id})"
                >
                    {{ payment.supply }}
                </Link>
            </el-form-item>
        </el-col>
        <el-col :span="12">
            <el-form-item label="Получатель">
                <el-input :model-value="payment.distributor_org" :readonly="true" style="width: 300px"/>
            </el-form-item>
            <el-form-item label="Комментарий">
                <el-input v-model="info.comment" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" type="textarea" style="width: 300px" :rows="3"/>
            </el-form-item>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";

const props = defineProps({
    payment: Object,
    traders: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    number: props.payment.number,
    created_at: props.payment.created_at,
    amount: props.payment.amount,

    trader_id: props.payment.trader_id,
    comment:  props.payment.comment,
})
const notEdit = computed(() => props.payment.completed);

function setInfo() {
    iSavingInfo.value = true
    info.created_at = func.datetime(info.created_at)

    router.visit(route('admin.accounting.payment.set-info', {payment: props.payment.id}), {
        method: "post",
        data: info,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
