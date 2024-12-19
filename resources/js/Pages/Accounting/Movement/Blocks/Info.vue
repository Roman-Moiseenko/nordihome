<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Убытие">
                    <el-select v-model="info.storage_out" @change="setInfo" :disabled="iSavingInfo || notEdit"
                               style="width: 260px">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"
                                   :readonly="notEdit"/>
                    </el-select>
                    <el-tooltip effect="dark" placement="top-start" content="Поменять местами">
                        <el-button @click="onReplace" plain type="primary" class="ml-1" :disabled="notEdit">
                            <i class="fa-sharp fa-light fa-arrow-up-arrow-down"></i>
                        </el-button>
                    </el-tooltip>
                </el-form-item>
                <el-form-item label="Назначение">
                    <el-select v-model="info.storage_in" @change="setInfo" :disabled="iSavingInfo || notEdit"
                               style="width: 260px">
                        <el-option v-for="item in storages" :key="item.id" :value="item.id" :label="item.name"
                                   :readonly="notEdit"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Основание">
                    <Link v-if="movement.arrival_id" type="primary"
                          :href="route('admin.accounting.arrival.show', {arrival: movement.arrival_id})" class="ml-2">
                        Приходная накладная {{ movement.arrival_text }}
                    </Link>
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
    movement: Object,
    storages: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    document: {
        number: props.movement.number,
        created_at: props.movement.created_at,
        incoming_number: props.movement.incoming_number,
        incoming_at: props.movement.incoming_at,
        comment: props.movement.comment,
    },
    storage_out: props.movement.storage_out.id,
    storage_in: props.movement.storage_in.id,

})
const notEdit = computed(() => props.movement.completed);

function onReplace() {
    let buffer = info.storage_out
    info.storage_out = info.storage_in
    info.storage_in = buffer
    setInfo();
}
function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.movement.set-info', {movement: props.movement.id}), {
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
