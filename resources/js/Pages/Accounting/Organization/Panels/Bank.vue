<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-money-check-dollar-pen"></i>
                <span> Банковские данные</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-6 grid-cols-1 divide-x">
            <div class="p-4 col-span-6">
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="БИК">
                        <EditField :field="organization.bik" @update:field="setBik" />
                    </el-descriptions-item>
                    <el-descriptions-item label="Название банка">
                        <template v-if="organization.foreign">
                            <EditField :field="organization.bank_name" @update:field="setBank" />
                        </template>
                        <template v-else>
                        {{ organization.bank_name }}
                        </template>
                    </el-descriptions-item>
                    <el-descriptions-item label="Корр./счет">
                        {{ organization.corr_account }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Расчетный счет">
                        <EditField :field="organization.pay_account" @update:field="saveAccount" />
                    </el-descriptions-item>
                </el-descriptions>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {defineProps, ref} from "vue";
import {func} from '@Res/func.js'
import {router} from '@inertiajs/vue3'
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    organization: Array,
})


function saveAccount(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {pay_account: val})
}
function setBik(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {bik: val})
}
function setBank(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {bank: val})
}
</script>
