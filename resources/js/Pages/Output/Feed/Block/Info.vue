<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="12">
                <el-form-item label="Название фида">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Название">
                    <el-input v-model="info.setTitle"/>
                </el-form-item>
            </el-col>
            <el-col :span="12">
                <el-form-item label="Показывать пред.цену">
                    <el-switch v-model="info.setPreprice"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.setDescription" type="textarea" :rows="3"/>
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

<script setup lang="ts">
import {reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    feed: Object,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.feed.name ?? '',
    setPreprice: !!props.feed.setPreprice,
    setTitle: props.feed.setTitle ?? '',
    setDescription: props.feed.setDescription ?? '',
}

const info = reactive({...initialInfo})

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        if (JSON.stringify(info[key]) !== JSON.stringify(initialInfo[key])) return true
    }
    return false
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    router.visit(
        route('admin.output.feed.update', {feed: props.feed.id}), {
            method: "post",
            data: {...info},
            preserveScroll: true,
            preserveState: true,
            onSuccess: page => {
                Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
            }
        }
    );
}
</script>
