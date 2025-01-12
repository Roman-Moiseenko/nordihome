<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-form label-width="auto">
                <h2>Получатель</h2>
                <el-form-item label="Фамилия">
                    <el-input v-model="info.recipient.surname" @change="setInfo" :disabled="disabled" style="max-width: 300px;" />
                </el-form-item>
                <el-form-item label="Имя">
                    <el-input v-model="info.recipient.firstname" @change="setInfo" :disabled="disabled" style="max-width: 300px;" />
                </el-form-item>
                <el-form-item label="Отчество">
                    <el-input v-model="info.recipient.secondname" @change="setInfo" :disabled="disabled" style="max-width: 300px;" />
                </el-form-item>
                <h2 class="ml-2">Контакты</h2>
                <el-form-item label="Телефон">
                    <el-input v-model="info.phone" @change="setInfo" :disabled="disabled" :formatter="val => func.phone(val)" style="max-width: 250px;"/>
                </el-form-item>
                <el-form-item label="Комментарий">
                    <el-input v-model="info.address" @change="setInfo" :disabled="disabled" type="textarea" rows="2"/>
                </el-form-item>
            </el-form>
            <el-radio-group v-if="expense.is_delivery" v-model="info.delivery" @change="setInfo" :disabled="disabled">
                <el-radio value="402">Доставка по региону</el-radio>
                <el-radio value="403">Доставка по РФ</el-radio>
            </el-radio-group>
            <div>
                Общий вес <el-tag type="warning" effect="dark">{{ expense.weight }} кг</el-tag>
                Общий объем <el-tag type="warning" effect="dark">{{ expense.volume }} м3</el-tag>
            </div>
        </el-col>
        <el-col :span="16">
            Календарь отгрузок
        </el-col>
    </el-row>

</template>

<script setup lang="ts">
import {computed, defineProps, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import {func} from  "@Res/func.js"

const props = defineProps({
    expense: Object,

})
console.log(props.expense)
const iSavingInfo = ref(false)

const disabled = computed(() => {
    return iSavingInfo.value || !props.expense.status.is_new
})

const info = reactive({
    recipient: props.expense.recipient,
    phone: props.expense.phone,
    address: props.expense.address.address,
    comment: props.expense.comment,
    type: props.expense.type,

})

function setInfo() {
    iSavingInfo.value = true
/*
    if (info.bank_payment.date !== null || info.bank_payment.date !== undefined) {
        info.bank_payment.date = func.date(info.bank_payment.date)
    }*/
    router.visit(route('admin.order.expense.set-info', {expense: props.expense.id}), {
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
