<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>

    <div class="mt-3 p-5 bg-white rounded-lg">
        <div class="flex flex-wrap gap-3">
            <el-button type="danger" :loading="loading.clearAll" @click="onClearAll">
                Очистить весь кеш
            </el-button>
            <el-button type="warning" :loading="loading.createThumbs" @click="onCreateThumbs">
                Пересоздать все кешированные изображения
            </el-button>
            <el-button type="warning" :loading="loading.reCreateThumbs" @click="onReCreateThumbs">
                Создать недостающие кешированные изображения
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
    createThumbs: false,
    reCreateThumbs: false,
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
async function onReCreateThumbs() {
    if (!await confirmRun('Вы действительно хотите удалить пересоздать все кешированные изображения?')) return
    loading.reCreateThumbs = true
    try {
        await api.post(route('admin.setting.cache.recreate-thumbs'), {})
    } finally {
        loading.reCreateThumbs = false
    }
}
async function onCreateThumbs() {
    if (!await confirmRun('Вы действительно хотите создать недостающие все кешированные изображения?')) return
    loading.createThumbs = true
    try {
        await api.post(route('admin.setting.cache.create-thumbs'), {})
    } finally {
        loading.createThumbs = false
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
