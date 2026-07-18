<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="6">
                <el-form-item label="Внутр.имя">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable placeholder="Slug"/>
                </el-form-item>

                <el-form-item label="Опубликованно">
                    <el-date-picker v-model="info.publishedAt" type="datetime"/>
                </el-form-item>

                <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button v-if="hasChanges" type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-col>
            <el-col :span="4">
                <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                    <PhotoDTO model-type="content.post" :entity-id="post.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="7">

                <el-form-item label="Статья">
                    <el-input v-model="info.caption"/>
                </el-form-item>
                <el-form-item label="Фрагмент">
                    <el-input v-model="info.fragment" type="textarea" :rows="5"/>
                </el-form-item>
            </el-col>
            <el-col :span="7">
                <el-form-item label="title">
                    <el-input v-model="info.metaTitle"/>
                </el-form-item>
                <el-form-item label="description">
                    <el-input v-model="info.metaDescription" type="textarea" :rows="5"/>
                </el-form-item>
            </el-col>
        </el-row>
    </el-form>

</template>

<script setup lang="ts">
import {ref, defineProps, reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import HelpBlock from "@Comp/HelpBlock.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";
import {route} from "ziggy-js";

const editInfo = ref(false)
const props = defineProps({
    post: Object,
    //templates: Array<ISelectItem>,
})
console.log(props.post)
// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.post.name,
    caption: props.post.caption,
    title: props.post.title,
    fragment: props.post.fragment,
    slug: props.post.slug,
    publishedAt: props.post.publishedAt,
    metaTitle: props.post.meta.title,
    metaDescription: props.post.meta.description,
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
    info.published_at = func.datetime(info.published_at)
    router.visit(
        route('admin.content.post.set-info', {id: props.post.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                editInfo.value = false;
            }
        }
    );
}


</script>


<style scoped>

</style>
