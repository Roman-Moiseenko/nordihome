<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-circle-info"></i>
                <span> Общая информация</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-6 grid-cols-1 divide-x">
            <div class="p-4 col-span-6">
                <el-descriptions :column="2" border>
                    <el-descriptions-item label="Сокращенное имя">
                        <template v-if="organization.foreign">
                            <EditField :field="organization.shortName" @update:field="setShort" />
                        </template>
                        <template v-else>
                            {{ organization.shortName }}
                        </template>
                    </el-descriptions-item>
                    <el-descriptions-item label="Полное имя">
                        <template v-if="organization.foreign">
                            <EditField :field="organization.fullName" @update:field="setFull" />
                        </template>
                        <template v-else>
                            {{ organization.fullName }}
                        </template>
                    </el-descriptions-item>
                    <el-descriptions-item label="ИНН">
                        {{ organization.inn }}
                    </el-descriptions-item>
                    <el-descriptions-item label="КПП">
                        {{ organization.kpp }}
                    </el-descriptions-item>
                    <el-descriptions-item label="ОГРН">
                        {{ organization.ogrn }}
                    </el-descriptions-item>
                </el-descriptions>
                <el-button type="warning" plain @click="onRefresh" class="mt-3">Обновить данные</el-button>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    organization: Object,
})

function onRefresh(){
    router.post(route('admin.accounting.organization.refresh', {id: props.organization.id}))
}
function setShort(val) {
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {shortName: val}, {preserveScroll: true})
}
function setFull(val) {
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {fullName: val}, {preserveScroll: true})
}
</script>
