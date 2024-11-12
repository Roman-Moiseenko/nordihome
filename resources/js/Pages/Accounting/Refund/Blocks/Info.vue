<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <el-form-item label="Поставщик">
                <el-input :model-value="refund.distributor + ' (' + refund.distributor_org + ')'" :readonly="true" />
            </el-form-item>
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
            <el-form-item label="Хранилище">
                <el-select v-model="info.storage_id" @change="setInfo" :disabled="iSavingInfo || notEdit" style="width: 260px">
                    <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"  :readonly="notEdit"/>
                </el-select>
            </el-form-item>
        </el-col>
        <el-col :span="12">
            <el-form-item label="Курс валюты">
                <el-input v-model="info.exchange_fix" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit" style="width: 160px"/>
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
    refund: Object,
    storages: Array,
    operations: Array,
})
const iSavingInfo = ref(false)

const info = reactive({
    number: props.refund.number,
    created_at: props.refund.created_at,
    incoming_number: props.refund.incoming_number,
    incoming_at: props.refund.incoming_at,
    //exchange_fix: props.refund.exchange_fix,
    comment:  props.refund.comment,
    storage_id: props.refund.storage_id,
})
const notEdit = computed(() => props.refund.completed);

function setInfo() {
    iSavingInfo.value = true
    info.created_at = func.datetime(info.created_at)
    info.incoming_at = func.date(info.incoming_at)
    router.visit(route('admin.accounting.refund.set-info', {refund: props.refund.id}), {
        method: "post",
        data: info,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
