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
            <el-form-item label="№ вход.док.">
                <el-input v-model="info.incoming_number" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" style="width: 160px"/>
                <span class="text-gray-500 px-4">от</span>
                <el-date-picker v-model="info.incoming_at" type="date"
                                @change="setInfo" :disabled="iSavingInfo"
                                style="width: 160px"
                                :readonly="notEdit"
                />
            </el-form-item>
        </el-col>
        <el-col :span="12">
            <el-form-item label="Курс валюты">
                <el-input v-model="info.exchange_fix" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" style="width: 160px"/>
            </el-form-item>
            <el-form-item label="Комментарий">
                <el-input v-model="info.comment" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" type="textarea" style="width: 300px" :rows="1"/>
            </el-form-item>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    supply: Object,
})
const iSavingInfo = ref(false)
const info = reactive({
    number: props.supply.number,
    created_at: props.supply.created_at,
    incoming_number: props.supply.incoming_number,
    incoming_at: props.supply.incoming_at,
    exchange_fix: props.supply.exchange_fix,
    comment:  props.supply.comment,
})
const notEdit = computed(() => props.supply.completed);

function setInfo() {
    iSavingInfo.value = true
    info.created_at = func.datetime(info.created_at)
    info.incoming_at = func.date(info.incoming_at)
    router.visit(route('admin.accounting.supply.set-info', {supply: props.supply.id}), {
        method: "post",
        data: info,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
