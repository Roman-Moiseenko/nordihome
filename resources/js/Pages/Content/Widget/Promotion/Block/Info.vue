<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="8">
                <el-form-item label="Виджет">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="info.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>

            </el-col>
            <el-col :span="8">
                <el-form-item label="Банер">
                    <el-select v-model="info.banner_id" clearable>
                        <el-option v-for="item in banners" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Акция">
                    <el-select v-model="info.promotion_id" clearable>
                        <el-option v-for="item in promotions" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8">
                <el-form-item label="Заголовок">
                    <el-input v-model="info.caption"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="3"/>
                </el-form-item>
            </el-col>
        </el-row>
        <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
            Отмена
        </el-button>
        <el-button v-if="hasChanges" type="success" @click="onSetInfo">
            Сохранить
        </el-button>
    </el-form>
</template>

<script setup>
import {reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    widget: Object,
    templates: Array,
    banners: Array,
    promotions: Array,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.widget.name,
    template: props.widget.template,
    banner_id: props.widget.banner_id ?? null,
    promotion_id: props.widget.promotion_id ?? null,
    caption: props.widget.caption ?? '',
    description: props.widget.description ?? '',
}

const info = reactive({...initialInfo})

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        const a = JSON.stringify(info[key])
        const b = JSON.stringify(initialInfo[key])
        if (a !== b) return true
    }
    return false
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    router.visit(route('admin.content.widget.promotion.set-widget', {widget: props.widget.id}), {
        method: "post",
        data: {...info},
        onSuccess: page => {
            Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
        }
    })
}

</script>

<style scoped>

</style>
