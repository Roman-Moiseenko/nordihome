<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="4">
                <PhotoDTO
                    label="Изображение для каталога"
                    model-type="content.post-category"
                    :entity-id="category.id"
                    type="image"
                />
            </el-col>
            <el-col :span="10">
                <el-form-item label="Название рубрики">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable placeholder="Slug"/>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="info.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Записей на странице">
                    <el-input v-model="info.paginate"/>
                </el-form-item>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Заголовок">
                    <el-input v-model="info.title"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="2"/>
                </el-form-item>
                <el-form-item label="Meta-Title">
                    <el-input v-model="info.meta_title"/>
                </el-form-item>
                <el-form-item label="Meta-Description">
                    <el-input v-model="info.meta_description" type="textarea" :rows="2"/>
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
    category: Object,
    templates: Array,
    post_templates: Array,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.category.name,
    slug: props.category.slug ?? '',
    template: props.category.template,
    paginate: props.category.paginate ?? '',
    title: props.category.title ?? '',
    description: props.category.description ?? '',
    meta_title: props.category.meta?.title ?? '',
    meta_description: props.category.meta?.description ?? '',
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
        route('admin.content.post-category.set-info', {category: props.category.id}), {
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
