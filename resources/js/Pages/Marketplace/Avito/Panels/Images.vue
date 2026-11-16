<template>
    <el-tab-pane name="images">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-images"></i>
                <span> Изображения</span>
            </span>
        </template>

        <div class="flex gap-6">
            <div style="flex: 1;">
                <div class="font-medium mb-3">Галерея объявления</div>
                <PhotoDTO
                    :key="galleryKey"
                    model-type="marketplace.avito-product"
                    :entity-id="productId"
                    type="gallery"
                    :upload-route="uploadRoute"
                />
            </div>

            <div style="width: 220px;">
                <template v-if="productCatalogId">
                    <div class="font-medium mb-3">Изображения товара</div>
                    <div v-loading="loading">
                        <div
                            v-for="photo in photos"
                            :key="photo.id"
                            class="flex items-center mb-2 border border-gray-200 rounded p-1"
                        >
                            <el-button
                                circle
                                size="small"
                                title="Скопировать в объявление"
                                @click="copyPhoto(photo.id)"
                            >
                                <i class="fa-light fa-arrow-left"></i>
                            </el-button>
                            <el-image
                                :src="photo.thumb || photo.url"
                                fit="cover"
                                class="ml-2"
                                style="width: 120px; height: 90px; border-radius: 4px;"
                                :preview-src-list="[photo.url]"
                                preview-teleported
                            />
                        </div>
                        <div v-if="!loading && !photos.length" class="text-gray-400 text-sm">
                            Нет изображений
                        </div>
                    </div>
                </template>

                <div class="font-medium mb-3 mt-4">Общие изображения</div>
                <div v-loading="commonLoading">
                    <div
                        v-for="image in commonImages"
                        :key="image.id"
                        class="flex items-center mb-2 border border-gray-200 rounded p-1"
                    >
                        <el-button
                            circle
                            size="small"
                            title="Скопировать в объявление"
                            @click="copyCommonImage(image.imageId)"
                        >
                            <i class="fa-light fa-arrow-left"></i>
                        </el-button>
                        <el-image
                            :src="image.image"
                            fit="cover"
                            class="ml-2"
                            style="width: 120px; height: 90px; border-radius: 4px;"
                            :preview-src-list="[image.image]"
                            preview-teleported
                        />
                    </div>
                    <div v-if="!commonLoading && !commonImages.length" class="text-gray-400 text-sm">
                        Нет общих изображений
                    </div>
                </div>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { route } from "ziggy-js";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    productCatalogId: {
        type: Number,
        default: null,
    },
})

const photos = ref<any[]>([])
const loading = ref(false)

const commonImages = ref<any[]>([])
const commonLoading = ref(false)

// Ключ для пересоздания галереи после копирования/загрузки.
const galleryKey = ref(0)

// Ручная загрузка авито-изображений с обработкой (обрезка 1280×960 + водяной знак).
const uploadRoute = computed(() => route('admin.marketplace.avito.product.image', { id: props.productId }))

async function loadPhotos() {
    if (!props.productCatalogId) {
        photos.value = []
        return
    }

    loading.value = true
    try {
        const response = await axios.get(route('admin.photo.get-by-entity'), {
            params: {
                imageableId: props.productCatalogId,
                modelType: 'catalog.product',
                type: 'gallery',
            },
        })
        photos.value = Array.isArray(response.data) ? response.data : []
    } catch {
        photos.value = []
    } finally {
        loading.value = false
    }
}

async function loadCommonImages() {
    commonLoading.value = true
    try {
        const response = await axios.get(route('admin.marketplace.avito.common-images.list'))
        commonImages.value = Array.isArray(response.data) ? response.data : []
    } catch {
        commonImages.value = []
    } finally {
        commonLoading.value = false
    }
}

function copyPhoto(photoId: number) {
    router.post(route('admin.marketplace.avito.product.copy-photo', { id: props.productId }), {
        photoIds: [photoId],
    }, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            galleryKey.value++
        },
    })
}

function copyCommonImage(photoId: number | null) {
    if (!photoId) return

    router.post(route('admin.marketplace.avito.product.copy-common-image', { id: props.productId }), {
        photoId,
    }, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            galleryKey.value++
        },
    })
}

onMounted(() => {
    loadPhotos()
    loadCommonImages()
})

watch(() => props.productCatalogId, loadPhotos)
</script>

<style scoped>

</style>
