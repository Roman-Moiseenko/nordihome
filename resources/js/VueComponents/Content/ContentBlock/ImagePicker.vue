<template>
    <!-- Если изображение уже выбрано — показываем превью -->
    <div v-if="modelValue" class="selected-image-wrapper">
        <div class="selected-image-preview">
            <el-image
                :src="modelValue.src"
                fit="cover"
                class="selected-image-img"
            />
            <div class="selected-image-actions">
                <el-button size="small" type="primary" @click="openPicker">
                    Выбрать другое
                </el-button>
                <el-button size="small" type="danger" plain @click="removeImage">
                    Удалить
                </el-button>
            </div>
        </div>
    </div>

    <!-- Если изображения нет — кнопка "Выбрать изображение" -->
    <div v-else class="empty-image-wrapper">
        <el-button type="primary" @click="openPicker" class="select-image-btn">
            <el-icon class="mr-1"><Plus /></el-icon>
            Выбрать изображение
        </el-button>
    </div>

    <!-- БОЛЬШОЕ ДИАЛОГОВОЕ ОКНО ВЫБОРА ИЗОБРАЖЕНИЯ -->
    <el-dialog
        v-model="dialogVisible"
        title="Выбор изображения"
        width="95%"
        top="2vh"
        :close-on-click-modal="false"
        class="image-picker-dialog"
    >
        <div class="image-picker-layout">
            <!-- ЛЕВАЯ ПАНЕЛЬ: список галерей -->
            <div class="gallery-list-panel">
                <div
                    v-for="gallery in galleries"
                    :key="gallery.id"
                    class="gallery-tab-item"
                    :class="{ active: selectedGalleryId === gallery.id }"
                    @click="selectedGalleryId = gallery.id"
                >
                    <div class="gallery-tab-name">{{ gallery.name }}</div>
                    <div class="gallery-tab-count">{{ gallery.images.length }}</div>
                </div>
            </div>

            <!-- ЦЕНТРАЛЬНАЯ ПАНЕЛЬ: сетка изображений -->
            <div class="images-grid-panel">
                <!-- Кнопка загрузки с диска (внутри окна) -->
                <div class="upload-area">
                    <el-upload
                        ref="uploadRef"
                        :auto-upload="true"
                        :show-file-list="false"
                        :action="uploadAction"
                        :headers="uploadHeaders"
                        :on-success="onUploadSuccess"
                        :on-error="onUploadError"
                    >
                        <el-button type="success">
                            <el-icon class="mr-1"><Upload /></el-icon>
                            Загрузить с диска
                        </el-button>
                    </el-upload>
                    <span v-if="uploading" class="text-sm text-gray-400 ml-2">Загрузка...</span>
                </div>

                <!-- Сетка изображений выбранной галереи -->
                <div v-if="currentGalleryImages.length > 0" class="images-grid">
                    <div
                        v-for="image in currentGalleryImages"
                        :key="image.id"
                        class="image-grid-item"
                        :class="{ selected: selectedImage?.id === image.id }"
                        @click="selectImage(image)"
                    >
                        <el-image
                            :src="image.url"
                            fit="cover"
                            class="grid-image"
                            loading="lazy"
                        />
                        <div class="image-id-badge">#{{ image.id }}</div>
                    </div>
                </div>
                <el-empty v-else description="В этой галерее нет изображений" />
            </div>

            <!-- ПРАВАЯ ПАНЕЛЬ: информация о выбранном изображении -->
            <div class="image-info-panel">
                <template v-if="selectedImage">
                    <div class="info-preview">
                        <el-image
                            :src="selectedImage.url"
                            fit="contain"
                            class="info-preview-img"
                        />
                    </div>
                    <el-form class="info-form" label-position="top" size="small">
                        <el-form-item label="ID фото">
                            <el-input :model-value="selectedImage.id" disabled />
                        </el-form-item>
                        <el-form-item label="Alt для фото">
                            <el-input
                                v-model="selectedImage.alt"
                                placeholder="Alt для SEO"
                            />
                        </el-form-item>
                        <el-form-item label="Заголовок">
                            <el-input
                                v-model="selectedImage.title"
                                placeholder="Заголовок"
                            />
                        </el-form-item>
                        <el-form-item label="Описание">
                            <el-input
                                v-model="selectedImage.description"
                                placeholder="Описание"
                                type="textarea"
                                :rows="3"
                            />
                        </el-form-item>
                    </el-form>
                    <div class="info-actions">
                        <el-button type="primary" @click="confirmSelection">
                            Выбрать
                        </el-button>
                        <el-button @click="dialogVisible = false">
                            Отмена
                        </el-button>
                    </div>
                </template>
                <el-empty v-else description="Выберите изображение" />
            </div>
        </div>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { Plus, Upload } from '@element-plus/icons-vue'
import axios from 'axios'
import api from '@Res/api'
// @ts-ignore
import { route } from 'ziggy-js'

interface ImageData {
    id: number | null
    url: string
    src: string
    alt: string
    title: string
    description: string
}

interface GalleryData {
    id: number
    name: string
    slug: string
    images: ImageData[]
}

const props = defineProps<{
    modelValue: ImageData | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: ImageData | null): void
}>()

const dialogVisible = ref(false)
const galleries = ref<GalleryData[]>([])
const selectedGalleryId = ref<number | null>(null)
const selectedImage = ref<ImageData | null>(null)
const uploading = ref(false)
const uploadRef = ref<any>(null)

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
const uploadHeaders = computed(() => ({
    'X-CSRF-TOKEN': csrf,
}))
const uploadAction = computed(() => route('admin.content.gallery.upload-to-widget'))

const currentGalleryImages = computed(() => {
    const gallery = galleries.value.find(g => g.id === selectedGalleryId.value)
    return gallery ? gallery.images : []
})

async function loadGalleries() {
    try {
        const data = await api.post(route('admin.content.gallery.get-tree'), {}, { showSuccess: false })
        galleries.value = data

        // Выбираем первую галерею по умолчанию
        if (galleries.value.length > 0 && !selectedGalleryId.value) {
            selectedGalleryId.value = galleries.value[0].id
        }
    } catch (e) {
        console.error('Ошибка загрузки галерей:', e)
    }
}

function openPicker() {
    selectedImage.value = props.modelValue ? { ...props.modelValue } : null
    loadGalleries()
    dialogVisible.value = true
}

function selectImage(image: ImageData) {
    selectedImage.value = { ...image }
}

async function confirmSelection() {
    if (!selectedImage.value) return

    // Сохраняем изменения alt/title/description на сервер
    try {
        await api.post(
            route('admin.content.gallery.image-set-widget', { photo: selectedImage.value.id }),
            {
                alt: selectedImage.value.alt,
                title: selectedImage.value.title,
                description: selectedImage.value.description,
            },
            { showSuccess: false }
        )
    } catch (e) {
        console.error('Ошибка сохранения метаданных изображения:', e)
    }

    // Нормализуем: src = url
    const result: ImageData = {
        id: selectedImage.value.id,
        url: selectedImage.value.url,
        src: selectedImage.value.url,
        alt: selectedImage.value.alt,
        title: selectedImage.value.title,
        description: selectedImage.value.description,
    }

    emit('update:modelValue', result)
    dialogVisible.value = false
}

function removeImage() {
    emit('update:modelValue', null)
}

function onUploadSuccess(response: any) {
    uploading.value = false
    // После загрузки добавляем изображение в галерею "Виджет" и обновляем список
    const newImage: ImageData = {
        id: response.id,
        url: response.url,
        src: response.url,
        alt: response.alt || '',
        title: response.title || '',
        description: response.description || '',
    }

    // Находим галерею "Виджет" и добавляем в неё
    const widgetGallery = galleries.value.find(g => g.slug === 'widget')
    if (widgetGallery) {
        widgetGallery.images.unshift(newImage)
        selectedGalleryId.value = widgetGallery.id
    }

    // Автоматически выбираем загруженное изображение
    selectImage(newImage)
}

function onUploadError() {
    uploading.value = false
}
</script>

<style scoped>
/* Большое диалоговое окно */
.image-picker-dialog :deep(.el-dialog__body) {
    padding: 0;
    height: calc(95vh - 100px);
    overflow: hidden;
}

.image-picker-layout {
    display: flex;
    height: 100%;
}

/* Левая панель — список галерей */
.gallery-list-panel {
    width: 180px;
    min-width: 180px;
    border-right: 1px solid #e5e7eb;
    overflow-y: auto;
    background: #f9fafb;
}

.gallery-tab-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.15s;
}

.gallery-tab-item:hover {
    background: #e5e7eb;
}

.gallery-tab-item.active {
    background: #dbeafe;
    border-right: 3px solid #3b82f6;
    font-weight: 500;
}

.gallery-tab-name {
    font-size: 13px;
    color: #374151;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.gallery-tab-count {
    font-size: 11px;
    color: #9ca3af;
    background: #e5e7eb;
    border-radius: 10px;
    padding: 1px 6px;
    margin-left: 6px;
}

/* Центральная панель — сетка изображений */
.images-grid-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.upload-area {
    padding: 12px 16px;
    border-bottom: 1px solid #e5e7eb;
    background: #fff;
    display: flex;
    align-items: center;
}

.images-grid {
    flex: 1;
    overflow-y: auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
    padding: 12px 16px;
    align-content: start;
}

.image-grid-item {
    position: relative;
    border: 2px solid transparent;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    aspect-ratio: 1;
    transition: border-color 0.15s;
}

.image-grid-item:hover {
    border-color: #93c5fd;
}

.image-grid-item.selected {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

.grid-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.image-id-badge {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 4px;
    pointer-events: none;
}

/* Правая панель — информация об изображении */
.image-info-panel {
    width: 300px;
    min-width: 300px;
    border-left: 1px solid #e5e7eb;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    background: #fff;
}

.info-preview {
    margin-bottom: 16px;
    border-radius: 8px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 150px;
    max-height: 200px;
}

.info-preview-img {
    max-width: 100%;
    max-height: 200px;
    object-fit: contain;
}

.info-form {
    flex: 1;
}

.info-form :deep(.el-form-item) {
    margin-bottom: 12px;
}

.info-form :deep(.el-form-item__label) {
    padding-bottom: 2px;
    font-size: 12px;
    color: #6b7280;
}

.info-actions {
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
}

/* Выбранное изображение */
.selected-image-wrapper {
    margin-bottom: 12px;
}

.selected-image-preview {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.selected-image-img {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    object-fit: cover;
}

.selected-image-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.empty-image-wrapper {
    margin-bottom: 12px;
}

.select-image-btn {
    width: 100%;
}
</style>
