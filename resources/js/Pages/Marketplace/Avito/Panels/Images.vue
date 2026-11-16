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
                <PhotoDTO model-type="marketplace.avito-product" :entity-id="productId" type="gallery"/>
            </div>

            <div v-if="productCatalogId" style="width: 220px;">
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
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
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

function copyPhoto(photoId: number) {
    router.post(route('admin.marketplace.avito.product.copy-photo', { id: props.productId }), {
        photoIds: [photoId],
    }, {
        preserveScroll: true,
        preserveState: false,
    })
}

onMounted(loadPhotos)

watch(() => props.productCatalogId, loadPhotos)
</script>

<style scoped>

</style>
