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
                <el-form-item label="Организация заказчик">
                    <el-select v-model="info.customer_id"  @change="setInfo" :disabled="iSavingInfo || notEdit" filterable>
                        <el-option v-for="item in customers" :key="item.id" :value="item.id" :label="item.short_name + ' (' + item.inn +')'"/>
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
import AccountingDocument from "@Comp/Accounting/Document.vue";

const props = defineProps({
    surplus: Object,
    storages: Array,
    customers: Array,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.surplus.number,
        created_at: props.surplus.created_at,
        incoming_number: props.surplus.incoming_number,
        incoming_at: props.surplus.incoming_at,
        comment: props.surplus.comment,
    },
    storage_id: props.surplus.storage_id,
    customer_id: props.surplus.customer_id,

})
const notEdit = computed(() => props.surplus.completed || props.surplus.trashed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.surplus.set-info', {surplus: props.surplus.id}), {
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
