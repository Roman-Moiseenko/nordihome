<template>
    <div>
        <h2 v-if="label" class="font-medium mb-3">{{ label }}</h2>

        <!-- Одиночное изображение (image / icon) -->
        <el-upload
            v-if="type !== 'gallery'"
            ref="uploadRef"
            :action="uploadAction"
            list-type="picture-card"
            :limit="1"
            :auto-upload="false"
            :file-list="fileList"
            :on-change="onFileChange"
            :on-remove="handleRemove"
            :on-preview="handlePreview"
            :headers="{'X-CSRF-TOKEN': csrf}"
            :class="'file-uploader-one' + (mini ? ' mini' : '')"
        >
            <el-icon><Plus /></el-icon>
        </el-upload>

        <!-- Галерея (gallery) -->
        <el-upload
            v-if="type === 'gallery'"
            ref="uploadRef"
            :action="uploadAction"
            list-type="picture-card"
            :auto-upload="false"
            :file-list="fileList"
            :on-change="onFileChange"
            :on-remove="handleRemove"
            :on-preview="handlePreview"
            :headers="{'X-CSRF-TOKEN': csrf}"
        >
            <el-icon><Plus /></el-icon>
        </el-upload>

        <!-- Диалог предпросмотра и редактирования данных -->
        <el-dialog v-model="dialogVisible" width="90%">
            <div class="flex">
                <div style="width: 80%; height: 90vh">
                    <img :src="dialogImageUrl" alt="Preview Image" class="mx-auto" style="height: 100%; width: auto" />
                </div>
                <div class="bg-gray-100 p-2 border border-gray-300" style="width: 20%">
                    <el-form :model="form" label-width="auto">
                        <el-form-item label="ID фото">
                            <el-input v-model="form.photo_id" readonly />
                        </el-form-item>
                        <el-form-item label="Alt для фото" label-position="top">
                            <el-input v-model="form.alt" placeholder="Напишите Alt для SEO" />
                        </el-form-item>
                        <el-form-item label="Заголовок" label-position="top">
                            <el-input v-model="form.title" placeholder="Заголовок" />
                        </el-form-item>
                        <el-form-item label="Описание" label-position="top">
                            <el-input v-model="form.description" placeholder="Описание" type="textarea" :rows="3" />
                        </el-form-item>
                        <el-button type="primary" @click="onSubmitData">Сохранить</el-button>
                        <span v-if="dialogSave" class="text-lime-500 text-sm ml-3">Сохранено</span>
                    </el-form>
                    <div class="mt-5">
                        <el-input v-model="dialogImageUrl" readonly />
                        <el-button type="success" class="text-sm mt-2" @click="copyBuffer" plain>
                            Скопировать Url
                        </el-button>
                        <span v-if="dialogCopy" class="text-lime-500 text-sm ml-3">Скопировано</span>
                    </div>
                </div>
            </div>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { defineProps, ref, onMounted, computed, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import Sortable from 'sortablejs'
import { Plus } from '@element-plus/icons-vue'
import type { UploadFile, UploadProps, UploadUserFile } from 'element-plus'

const props = defineProps({
    label: String,
    entityId: { type: Number, required: true },
    modelType: { type: String, required: true },
    type: { type: String, default: 'image' },
    mini: { type: Boolean, default: false },
})

const csrf = document.querySelector('meta[name="csrf-token]')?.getAttribute('content') || ''

const uploadRef = ref()
const fileList = ref<UploadUserFile[]>([])
const dialogVisible = ref(false)
const dialogImageUrl = ref('')
const dialogCopy = ref(false)
const dialogSave = ref(false)

const form = reactive({
    photo_id: null as number | null,
    alt: '',
    title: '',
    description: '',
})

const uploadAction = computed(() => {
    return route('admin.photo.upload')
})

async function loadImages() {
    try {
        const response = await axios.get(route('admin.photo.get-by-entity'), {
            params: {
                imageableId: props.entityId,
                modelType: props.modelType,
                type: props.type,
            }
        })

        if (props.type === 'gallery' && Array.isArray(response.data)) {
            fileList.value = response.data.map((photo: any) => ({
                name: photo.url,
                url: photo.url,
                id: photo.id,
                alt: photo.alt || '',
                title: photo.title || '',
                description: photo.description || '',
            }))
        } else if (response.data && response.data.url) {
            fileList.value = [{
                name: response.data.url,
                url: response.data.url,
                id: response.data.id,
                alt: response.data.alt || '',
                title: response.data.title || '',
                description: response.data.description || '',
            }]
        }
    } catch {
        fileList.value = []
    }
}

// Загрузка файла
function onFileChange(uploadFile: UploadFile) {
    if (!uploadFile.raw) return

    const formData = new FormData()
    formData.append('file', uploadFile.raw)
    formData.append('imageableId', String(props.entityId))
    formData.append('modelType', props.modelType)
    formData.append('type', props.type)

    axios.post(route('admin.photo.upload'), formData, {
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'multipart/form-data',
        }
    }).then(response => {
        if (props.type === 'gallery') {
            const index = fileList.value.findIndex(f => f.uid === uploadFile.uid)
            if (index !== -1) {
                fileList.value[index].id = response.data.id
                fileList.value[index].url = response.data.url
                fileList.value[index].alt = response.data.alt || ''
                fileList.value[index].title = response.data.title || ''
                fileList.value[index].description = response.data.description || ''
            }
        } else {
            fileList.value = [{
                name: response.data.url,
                url: response.data.url,
                id: response.data.id,
                alt: response.data.alt || '',
                title: response.data.title || '',
                description: response.data.description || '',
            }]
        }
    }).catch(error => {
        console.error('Ошибка загрузки:', error)
    })
}

// Удаление — вызывается el-upload при клике на крестик
const handleRemove: UploadProps['onRemove'] = (uploadFile: UploadFile) => {
    const photoId = uploadFile.id

    if (!photoId) return // новый файл без id — просто удаляем из списка

    axios.delete(route('admin.photo.destroy', { id: photoId }), {
        headers: { 'X-CSRF-TOKEN': csrf }
    }).catch(error => {
        console.error('Ошибка удаления:', error)
    })
    // Не возвращаем false — el-upload сам удалит элемент из списка
}

// Предпросмотр
const handlePreview: UploadProps['onPreview'] = (uploadFile: UploadFile) => {
    dialogImageUrl.value = uploadFile.url!
    form.photo_id = uploadFile.id as number || null
    form.alt = (uploadFile as any).alt || ''
    form.title = (uploadFile as any).title || ''
    form.description = (uploadFile as any).description || ''
    dialogVisible.value = true
}

// Сохранение данных
function onSubmitData() {
    if (!form.photo_id) return

    dialogSave.value = true
    setTimeout(() => { dialogSave.value = false }, 2000)

    router.post(route('admin.photo.save-data', { id: form.photo_id }), {
        alt: form.alt,
        title: form.title,
        description: form.description,
    })
}

// Копирование URL
function copyBuffer() {
    dialogCopy.value = true
    setTimeout(() => { dialogCopy.value = false }, 2000)
    navigator.clipboard.writeText(dialogImageUrl.value)
}

// Сортировка для gallery
function initDragSort() {
    const el = document.querySelector('.el-upload-list')
    if (!el) return

    Sortable.create(el, {
        onEnd: ({ oldIndex, newIndex }: { oldIndex: number; newIndex: number }) => {
            const movedItem = fileList.value[oldIndex]
            fileList.value.splice(oldIndex, 1)
            fileList.value.splice(newIndex, 0, movedItem)

            fileList.value.forEach((file, index) => {
                if (file.id) {
                    axios.post(route('admin.photo.sort', { id: file.id }), {
                        sort: index,
                    }, {
                        headers: { 'X-CSRF-TOKEN': csrf }
                    }).catch(error => {
                        console.error('Ошибка сортировки:', error)
                    })
                }
            })
        }
    })
}

onMounted(() => {
    loadImages()
    if (props.type === 'gallery') {
        setTimeout(initDragSort, 500)
    }
})

watch(fileList, () => {
    if (props.type === 'gallery') {
        setTimeout(initDragSort, 100)
    }
}, { deep: true })
</script>

<style scoped>
.mini {
    :deep(.el-upload--picture-card) {
        --el-upload-picture-card-size: 48px;
    }
    :deep(.el-upload-list__item) {
        --el-upload-list-picture-card-size: 48px;
    }
}
</style>
