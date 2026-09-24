<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="4">
                <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                    <PhotoDTO model-type="content.page" :entity-id="page.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Страница">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable/>
                </el-form-item>
                <el-form-item label="Родительская">
                    <el-select v-model="info.parent_id" clearable filterable>
                        <el-option v-for="item in pages" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="info.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="10">
                <el-form-item label="svg иконка">
                    <el-input v-model="info.svg" type="textarea" :rows="3"/>
                </el-form-item>
                <el-form-item label="Мета Заголовок">
                    <el-input v-model="info.meta_title"/>
                </el-form-item>
                <el-form-item label="Мета Описание">
                    <el-input v-model="info.meta_description" type="textarea" :rows="3"/>
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
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    page: Object,
    templates: Array,
    pages: Array,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.page.name,
    slug: props.page.slug ?? '',
    parent_id: props.page.parent_id ?? null,
    template: props.page.template,
    svg: props.page.svg ?? '',
    meta_title: props.page.meta?.title ?? '',
    meta_description: props.page.meta?.description ?? '',
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
    router.visit(
        route('admin.content.page.set-info', {page: props.page.id}), {
            method: "post",
            data: {...info},
            onSuccess: page => {
                Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
            }
        }
    );
}

</script>

<style scoped>

</style>
