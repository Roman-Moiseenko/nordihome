<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Хранилище">
                    <el-input v-model="inventory.storage.name"  :disabled="true"
                               style="width: 260px">
                    </el-input>
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
    inventory: Object,
    storages: Array,
    customers: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    document: {
        number: props.inventory.number,
        created_at: props.inventory.created_at,
        incoming_number: props.inventory.incoming_number,
        incoming_at: props.inventory.incoming_at,
        comment: props.inventory.comment,
    },
    storage_id: props.inventory.storage_id,
    customer_id: props.inventory.customer_id,
})
const notEdit = computed(() => props.inventory.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.inventory.set-info', {inventory: props.inventory.id}), {
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
