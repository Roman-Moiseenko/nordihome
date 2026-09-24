<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>

    <div class="mt-3 p-5 bg-white rounded-lg">
        <div class="flex flex-wrap gap-3">
            <el-button type="danger" :loading="loading.clearAll" @click="onClearAll">
                Очистить весь кеш
            </el-button>
            <el-button type="warning" :loading="loading.clearImage" @click="onClearImage">
                Удалить кешированные изображения
            </el-button>
            <el-button type="primary" :loading="loading.recache" @click="onRecache">
                Пересоздать кеш
            </el-button>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { Head } from '@inertiajs/vue3'
import { reactive } from 'vue'
import { ElMessageBox } from 'element-plus'
import api from '@Res/api'

const props = defineProps({
    title: {
        type: String,
        default: 'Кеширование',
    },
})

const loading = reactive({
    clearAll: false,
    clearImage: false,
    recache: false,
})

async function confirmRun(message: string): Promise<boolean> {
    try {
        await ElMessageBox.confirm(message, 'Подтверждение действия', {
            confirmButtonText: 'Выполнить',
            cancelButtonText: 'Отмена',
            type: 'warning',
        })
        return true
    } catch {
        return false
    }
}

async function onClearAll() {
    if (!await confirmRun('Вы действительно хотите очистить весь кеш приложения?')) return
    loading.clearAll = true
    try {
        await api.post(route('admin.setting.cache.clear-all'), {})
    } finally {
        loading.clearAll = false
    }
}

async function onClearImage() {
    if (!await confirmRun('Вы действительно хотите удалить все кешированные изображения?')) return
    loading.clearImage = true
    try {
        await api.post(route('admin.setting.cache.clear-image'), {})
    } finally {
        loading.clearImage = false
    }
}

async function onRecache() {
    if (!await confirmRun('Вы действительно хотите пересоздать кеш?')) return
    loading.recache = true
    try {
        await api.post(route('admin.setting.cache.recache'), {})
    } finally {
        loading.recache = false
    }
}
</script>
