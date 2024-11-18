<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Склад списания">
                    <el-select v-model="info.storage_id" @change="setInfo" :disabled="iSavingInfo || notEdit"
                               style="width: 260px">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"
                                   :readonly="notEdit"/>
                    </el-select>
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
    departure: Object,
    storages: Array,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.departure.number,
        created_at: props.departure.created_at,
        incoming_number: props.departure.incoming_number,
        incoming_at: props.departure.incoming_at,
        comment: props.departure.comment,
    },
    storage_id: props.departure.storage_id,

})
const notEdit = computed(() => props.departure.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.departure.set-info', {departure: props.departure.id}), {
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
