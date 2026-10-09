<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-video"></i>
                <span> Видеообзоры</span>
            </span>
        </template>
        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <div v-else>
            <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
            <el-checkbox v-if="hasModification"
                v-model="form.modification"
                :checked="form.modification" class="checkbox-warning">
                Сохранять для всех товаров из Модификации
            </el-checkbox>
            <el-row :gutter="10" class="mt-2">
                <el-col :span="8">
                    <el-form label-width="auto">
                        <div v-for="(item, index) in form.videos" class="mt-2 bg-slate-200 rounded-md p-2 relative">
                            <div class="close-button-absolute">
                                <el-button type="danger" size="small" plain @click="onRemoveVideo(index)">X</el-button>
                            </div>
                            <el-form-item label="Ссылка на видео" label-position="top" class="">
                                <el-input v-model="item.url" @change="onAutoSave" :disabled="isSaving"/>
                            </el-form-item>
                            <el-form-item label="Заголовок" label-position="top" class="">
                                <el-input v-model="item.caption" @change="onAutoSave" :disabled="isSaving"/>
                            </el-form-item>
                            <el-form-item label="Описание" label-position="top" class="">
                                <el-input v-model="item.description" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="3" resize="none"/>
                            </el-form-item>
                        </div>
                        <el-button type="success" @click="onAddVideo" class="mt-1">Добавить</el-button>
                    </el-form>
                </el-col>
            </el-row>
            <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch, defineProps } from "vue";
import api from "@Res/api";
import {route} from "ziggy-js";
import {useProductPanel} from "./useProductPanel";

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: 'video',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)

const form = reactive({
    id: props.productId,
    videos: [],
    modification: false,
})

// Ошибки валидации (приходят из ответа на сохранение).
const saveErrors = reactive({})
const errors = computed(() => ({ ...saveErrors }))

// Загрузка данных панели с кэшем на 5 минут.
const { load, loading, loaded, setCache } = useProductPanel('video', async () => {
    return api.get(
        route('admin.catalog.product.edit.video', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

function applyData(data) {
    form.id = data.id
    form.videos = Array.isArray(data.videos) ? data.videos.map(v => ({ ...v })) : []
    hasModification.value = data.hasModification
}

// Автозагрузка при активации панели.
watch(() => props.active, async (active) => {
    if (!active) return
    const { data, fromCache } = await load()
    if (!fromCache && data) {
        applyData(data)
    }
}, { immediate: true })


function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onSave() {
    isSaving.value = true;
    Object.keys(saveErrors).forEach(key => delete saveErrors[key])

    api.post(
        route('admin.catalog.product.edit.video', { id: props.productId }),
        { ...form },
    ).then(data => {
        applyData(data)
        setCache(data)
    }).catch(error => {
        const errs = error?.response?.data?.errors
        if (errs && typeof errs === 'object') {
            for (const [key, value] of Object.entries(errs)) {
                saveErrors[key] = Array.isArray(value) ? value[0] : value
            }
        }
    }).finally(() => {
        isSaving.value = false
    })
}

function onAddVideo() {
    form.videos.push({
        url: 'https://',
        caption: '',
        description: '',
    })
    onAutoSave()
}
function onRemoveVideo(index) {
    form.videos.splice(index, 1)
    onAutoSave()
}
</script>

<style lang="scss">
.close-button-absolute {
    position: absolute;
    top: 8px;
    right: 8px;
}
</style>
