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
                            <EditField :field="organization.short_name" @update:field="setShort" />
                        </template>
                        <template v-else>
                            {{ organization.short_name }}
                        </template>
                    </el-descriptions-item>
                    <el-descriptions-item label="Полное имя">
                        <template v-if="organization.foreign">
                            <EditField :field="organization.full_name" @update:field="setFull" />
                        </template>
                        <template v-else>
                            {{ organization.full_name }}
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
                    <el-descriptions-item label="Холдинг">
                        <div v-show="!showEdit">
                            <span v-if="organization.holding_id"> {{ organization.holding.name }} </span>
                            <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                                <i class="fa-light fa-pen-to-square"></i>
                            </el-button>
                        </div>
                        <div v-show="showEdit">
                            <el-select
                                v-model="holding_id"
                                filterable
                                clearable
                                allow-create
                                default-first-option
                                :reserve-keyword="false"
                                style="width: 240px"
                                @change="saveHolding"
                            >
                                <el-option
                                    v-for="item in holdings"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                />
                            </el-select>
                            <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                                <i class="fa-light fa-xmark"></i>
                            </el-button>
                        </div>

                    </el-descriptions-item>
                </el-descriptions>
                <el-button type="warning" plain @click="onUpdate" class="mt-3">Обновить данные</el-button>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {defineProps, ref} from "vue";
import {router} from "@inertiajs/vue3";
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    organization: Array,
    holdings: Array,
})
const showEdit = ref(false)
const holding_id = ref(props.organization.holding_id)
function saveHolding() {
    if (holding_id.value === undefined) holding_id.value = null
    showEdit.value = false
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {holding_id: holding_id.value})

}
function onUpdate(){
    router.visit(route('admin.accounting.organization.update', {organization: props.organization.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function setShort(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {short_name: val})
}
function setFull(val) {
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {full_name: val})
}
</script>
