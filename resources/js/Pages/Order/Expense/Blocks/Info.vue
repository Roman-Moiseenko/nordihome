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


            </el-form>

        </el-col>
        <el-col :span="8">
            <el-form label-width="auto">
            <h2 class="ml-2">Контакты</h2>
            <el-form-item label="Телефон">
                <el-input v-model="info.phone" @change="setInfo" :disabled="disabled" :formatter="val => func.phone(val)" style="max-width: 250px;"/>
            </el-form-item>
            <el-form-item label="Адрес">
                <el-input v-model="info.address" @change="setInfo" :disabled="disabled" />
            </el-form-item>
            <el-form-item label="Комментарий">
                <el-input v-model="info.comment" @change="setInfo" :disabled="disabled" type="textarea" rows="2"/>
            </el-form-item>
            </el-form>
        </el-col>
        <el-col :span="8">
            <el-radio-group v-if="expense.is_delivery" v-model="info.type" @change="setInfo" :disabled="disabled">
                <el-radio-button :value="402">Доставка по региону</el-radio-button>
                <el-radio-button :value="403">Доставка по РФ</el-radio-button>
            </el-radio-group>
            <div class="mt-2">
                Общий вес <el-tag type="warning" effect="dark">{{ expense.weight }} кг</el-tag>
                Общий объем <el-tag type="warning" effect="dark">{{ expense.volume }} м3</el-tag>
            </div>
            <div v-if="info.type === 402" class="mt-2">
                <el-date-picker
                    v-model="form_calendar.date_at"
                    :disabled-date="disabledDate"
                    placeholder="Выберите дату доставки"
                    @change="findPeriod" :disabled="disabled"/>
                <div class="mt-3" v-if="periods.length > 0">
                    <h2>Время доставки</h2>
                    <el-radio-group v-model="form_calendar.period_id" style="display: block;" :disabled="disabled">
                        <el-row v-for="period in periods" class="mt-2" @change="setPeriod">
                            <el-radio border :value="period.id" >
                                {{ period.time_text }} ({{ period.free_weight }} кг, {{ period.free_volume }} м3)
                            </el-radio>
                        </el-row>

                    </el-radio-group>
                </div>
            </div>

        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import {computed, defineProps, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import {func} from  "@Res/func.js"
import axios from 'axios'

const props = defineProps({
    expense: Object,
})
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
const form_calendar = reactive({
    date_at: null,
    period_id: null,
})
const periods = ref([])
if (props.expense.calendar) {
    form_calendar.date_at = props.expense.calendar.date_at
    form_calendar.period_id = props.expense.calendar.period_id
    periods.value = [...props.expense.calendar.periods]
}

const disabledDate = (time: Date) => {
    return time.getTime() <= Date.now()
}

function findPeriod() {
    form_calendar.date_at = func.date(form_calendar.date_at)
    axios.post(route('admin.delivery.calendar.get-day'), {date: form_calendar.date_at}).then( result => {
        console.log(result.data)
        if (result.data.length > 0) {
            periods.value = [...result.data]
        } else {
            periods.value = []
        }
    })

}

function setPeriod() {
    iSavingInfo.value = true
    router.visit(
        route('admin.order.expense.set-delivery', {expense: props.expense.id}), {
            method: "post",
            data: {period_id: form_calendar.period_id},
            preserveScroll: true,
            preserveState: false,
            onSuccess: page => {
                iSavingInfo.value = false;
            }
        }
    )
}
</script>
