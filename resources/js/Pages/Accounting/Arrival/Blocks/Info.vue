<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document" :distributor="arrival.distributor"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Хранилище">
                    <el-select v-model="info.storage_id" @change="setInfo" :disabled="iSavingInfo || notEdit"
                               style="width: 260px">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"
                                   :readonly="notEdit"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Курс валюты">
                    <el-input v-model="info.exchange_fix" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit"
                              style="width: 160px"/>
                </el-form-item>
                <el-form-item label="Операция" v-if="arrival.distributor">
                    <el-select v-model="info.operation" @change="setInfo"
                               :disabled="arrival.supply_id || iSavingInfo || notEdit" style="width: 260px">
                        <el-option v-for="item in operations" :key="item.value" :value="item.value"
                                   :label="item.label"/>
                    </el-select>
                    <Link v-if="arrival.supply_id" type="primary"
                          :href="route('admin.accounting.supply.show', {supply: arrival.supply_id})" class="ml-2">
                        {{ arrival.supply }}
                    </Link>
                </el-form-item>
                <el-form-item label="ГТД" v-if="arrival.distributor && arrival.distributor.foreign">
                    <el-input v-model="info.gtd" @change="setInfo" :disabled="iSavingInfo" :readonly="notEdit"
                              style="width: 260px"/>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";

const props = defineProps({
    arrival: Object,
    storages: Array,
    operations: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    document: {
        number: props.arrival.number,
        created_at: props.arrival.created_at,
        incoming_number: props.arrival.incoming_number,
        incoming_at: props.arrival.incoming_at,
        comment: props.arrival.comment,
    },
    storage_id: props.arrival.storage_id,
    exchange_fix: props.arrival.exchange_fix,
    operation: props.arrival.operation,
    gtd: props.arrival.gtd,
})
const notEdit = computed(() => props.arrival.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.arrival.set-info', {arrival: props.arrival.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
