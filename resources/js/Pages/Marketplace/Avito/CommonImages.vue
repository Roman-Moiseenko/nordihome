<template>
    <Head><title>Общие изображения Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Общие изображения Авито</h1>

        <div class="my-3">
            <el-button type="primary" @click="addImage">Добавить новое изображение</el-button>
        </div>

        <div ref="listRef" class="p-5 bg-white rounded-md space-y-3">
            <div
                v-for="image in images"
                :key="image.id"
                class="flex items-center gap-4 p-3 border border-gray-200 rounded-md cursor-move"
            >
                <div class="shrink-0">
                    <el-upload
                        :show-file-list="false"
                        :auto-upload="false"
                        list-type="picture-card"
                        accept="image/*"
                        :on-change="(f) => onUpload(image, f)"
                    >
                        <img v-if="image.image" :src="image.image" class="w-20 h-20 object-cover rounded"/>
                        <el-icon v-else><Plus/></el-icon>
                    </el-upload>
                </div>

                <div class="flex-1">
                    <el-input
                        v-model="image.name"
                        placeholder="Название"
                        @change="saveName(image)"
                    />
                    <div class="mt-2">
                        <el-checkbox v-model="image.active" @change="saveActive(image)">
                            Активно
                        </el-checkbox>
                    </div>
                </div>

                <div class="shrink-0">
                    <el-button type="danger" plain size="small" @click="remove(image)">
                        Удалить
                    </el-button>
                </div>
            </div>

            <div v-if="!images.length" class="text-gray-400">
                Изображений пока нет. Нажмите «Добавить новое изображение».
            </div>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";
import Sortable from "sortablejs";
import { Plus } from '@element-plus/icons-vue';

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
})

const images = ref<any[]>([...props.images])
const listRef = ref<HTMLElement | null>(null)

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

function addImage() {
    router.post(route('admin.marketplace.avito.common-images.store'), {}, {
        preserveScroll: true,
        preserveState: false,
    })
}

async function onUpload(image: any, uploadFile: any) {
    if (!uploadFile.raw) return

    const form = new FormData()
    form.append('file', uploadFile.raw)
    form.append('imageableId', String(image.id))
    form.append('modelType', 'marketplace.avito-common-image')
    form.append('type', 'image')

    const { data } = await axios.post(route('admin.photo.upload'), form, {
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Content-Type': 'multipart/form-data',
        },
    })

    image.image = data.url
}

async function saveName(image: any) {
    await axios.post(
        route('admin.marketplace.avito.common-images.update', { id: image.id }),
        { name: image.name, active: image.active },
        { headers: { 'X-CSRF-TOKEN': csrf } }
    )
}

async function saveActive(image: any) {
    await axios.post(
        route('admin.marketplace.avito.common-images.update', { id: image.id }),
        { name: image.name, active: image.active },
        { headers: { 'X-CSRF-TOKEN': csrf } }
    )
}

function remove(image: any) {
    router.delete(route('admin.marketplace.avito.common-images.destroy', { id: image.id }), {
        preserveScroll: true,
        preserveState: false,
    })
}

function initSort() {
    if (!listRef.value) return

    Sortable.create(listRef.value, {
        animation: 150,
        onEnd: () => {
            const ids = images.value.map((image) => image.id)

            router.post(route('admin.marketplace.avito.common-images.sort'), { ids }, {
                preserveScroll: true,
                preserveState: true,
            })
        },
    })
}

onMounted(() => {
    initSort()
})
</script>
