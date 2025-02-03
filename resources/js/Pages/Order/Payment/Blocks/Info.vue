<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Способ оплаты">
                    <el-select v-model="info.method" :disabled="disabled" @change="setInfo">
                        <el-option v-for="item in methods" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item v-if="payment.is_cash || payment.is_card" label="Торговая точка">
                    <el-select v-model="info.storage_id" :disabled="disabled" @change="setInfo">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Сумма">
                    <el-input v-model="info.amount" :disabled="disabled"
                              @change="setInfo" :formatter="val => func.MaskInteger(val)"
                              style="width: 160px;">
                        <template #append>₽</template>
                    </el-input>
                </el-form-item>
                <el-form-item label="Комиссия">
                    <el-input v-model="info.commission"
                              :disabled="disabled" @change="setInfo"
                              :formatter="val => func.MaskFloat(val)" style="width: 100px;">
                        <template #append>%</template>
                    </el-input>
                </el-form-item>

                <el-form-item label="Комментарий">
                    <el-input v-model="info.comment" :disabled="disabled" @change="setInfo" />
                </el-form-item>
            </el-form>

        </el-col>
        <el-col :span="8">
            <el-form v-if="payment.is_account" label-width="auto">
                <h2>Банковские реквизиты</h2>
                <el-form-item  label="Номер">
                    <el-input v-model="info.bank_payment.number" :disabled="disabled" @change="setInfo"/>
                </el-form-item>

                <el-form-item  label="Дата">
                    <el-date-picker type="date" v-model="info.bank_payment.date" :disabled="disabled" @change="setInfo"/>
                </el-form-item>
                <el-form-item  label="БИК Плательщика">
                    <el-input v-model="info.bank_payment.bik_payer" :disabled="disabled" @change="setInfo"/>
                </el-form-item>
                <el-form-item  label="Р/счет Плательщика">
                    <el-input v-model="info.bank_payment.account_payer" :disabled="disabled" @change="setInfo"/>
                </el-form-item>
                <el-form-item  label="БИК Получателя">
                    <el-input v-model="info.bank_payment.bik_recipient" :disabled="disabled" @change="setInfo"/>
                </el-form-item>
                <el-form-item  label="Р/счет Получателя">
                    <el-input v-model="info.bank_payment.account_recipient" :disabled="disabled" @change="setInfo"/>
                </el-form-item>

                <el-form-item  label="Назначение платежа">
                    <el-input v-model="info.bank_payment.purpose" :disabled="disabled" @change="setInfo"/>
                </el-form-item>
            </el-form>
        </el-col>
        <el-col v-if="payment.order_id" :span="8">
            <Link type="primary" :href="route('admin.order.show', {order: payment.order_id})">Заказ № {{ payment.order.number }} от {{ func.date(payment.order.created_at) }} </Link>
        </el-col>

    </el-row>

</template>

<script setup lang="ts">
import {computed, defineProps, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import {func} from  "@Res/func.js"

const props = defineProps({
    payment: Object,
    methods: Array,
    storages: Array,
})
const iSavingInfo = ref(false)

const disabled = computed(() => {
    return iSavingInfo.value || props.payment.completed
})
const info = reactive({
    method: props.payment.method,
    amount: props.payment.amount,
    commission: props.payment.commission,
    comment: props.payment.comment,
    storage_id: props.payment.storage_id,
    bank_payment: props.payment.bank_payment,

})

function setInfo() {
    iSavingInfo.value = true

    if (info.bank_payment.date !== null || info.bank_payment.date !== undefined) {
        info.bank_payment.date = func.date(info.bank_payment.date)
    }
    router.visit(route('admin.order.payment.set-info', {payment: props.payment.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
