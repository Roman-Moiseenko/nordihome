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
                    <el-select v-model="info.parentId" clearable filterable>
                        <el-option v-for="item in useContent.pages" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="info.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Старый рендер">
                    <el-switch v-model="info.oldRender" />
                </el-form-item>
            </el-col>
            <el-col :span="10">
                <el-form-item label="svg иконка">
                    <el-input v-model="info.svg" type="textarea" :rows="3"/>
                </el-form-item>
                <el-form-item label="Мета Заголовок">
                    <el-input v-model="info.metaTitle"/>
                </el-form-item>
                <el-form-item label="Мета Описание">
                    <el-input v-model="info.metaDescription" type="textarea" :rows="3"/>
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
import {useContentStore} from "@Res/contentStore.ts";

const props = defineProps({
    page: Object,
    templates: Array,
})
const useContent = useContentStore()

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.page.name,
    slug: props.page.slug ?? '',
    parentId: props.page.parentId ?? null,
    template: props.page.template,
    svg: props.page.svg ?? '',
    metaTitle: props.page.meta?.title ?? '',
    metaDescription: props.page.meta?.description ?? '',
    oldRender: props.page.oldRender,
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
        route('admin.content.page.update', {id: props.page.id}), {
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
