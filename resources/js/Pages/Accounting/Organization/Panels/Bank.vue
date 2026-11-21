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
                            <EditField :field="organization.bankName" @update:field="setBank" />
                        </template>
                        <template v-else>
                        {{ organization.bankName }}
                        </template>
                    </el-descriptions-item>
                    <el-descriptions-item label="Корр./счет">
                        {{ organization.corrAccount }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Расчетный счет">
                        <EditField :field="organization.payAccount" @update:field="saveAccount" />
                    </el-descriptions-item>
                </el-descriptions>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {router} from '@inertiajs/vue3'
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    organization: Object,
})

function saveAccount(val) {
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {payAccount: val}, {preserveScroll: true})
}
function setBik(val) {
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {bik: val}, {preserveScroll: true})
}
function setBank(val) {
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {bank: val}, {preserveScroll: true})
}
</script>
