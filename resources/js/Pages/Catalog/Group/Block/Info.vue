<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="4">
                <el-tooltip content="Изображение для карточек" placement="top-start" effect="dark">
                    <PhotoDTO model-type="catalog.group" :entity-id="group.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Название группы">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable placeholder="Slug"/>
                </el-form-item>
                <el-form-item label="Страница на сайте">
                    <el-switch v-model="info.published"/>
                </el-form-item>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
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
    group: Object,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.group.name,
    slug: props.group.slug ?? '',
    description: props.group.description ?? '',
    published: !!props.group.published,
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
        route('admin.catalog.group.set-info', {group: props.group.id}), {
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
