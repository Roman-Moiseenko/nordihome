<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">  {{ gallery.name }}  </h1>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <GalleryInfo :gallery="gallery" :errors="errors"/>
    </div>
    <el-upload
        ref="upload"
        v-model:file-list="fileList"
        :action="route('admin.page.gallery.image-add', {gallery: props.gallery.id})"
        list-type="picture-card"
        :on-preview="handlePictureCardPreview"
        :on-success="onSuccess"
        :before-remove="handleRemove"
        :headers="{'X-CSRF-TOKEN': csrf}"
    >
        <el-icon>
            <Plus/>
        </el-icon>
    </el-upload>
    <el-dialog v-model="dialogVisible" width="90%">
        <div class="flex">
            <div style="width: 80%; height: 90vh">
                <img :src="dialogImageUrl" alt="Preview Image" class="mx-auto" style="height: 100%; width: auto"/>
            </div>
            <div class="bg-gray-100 p-2 border border-gray-300" style="width: 20%">
                <el-form :model="form" label-width="auto">
                    <el-form-item label="ID фото">
                        <el-input v-model="form.photo_id" readonly/>
                    </el-form-item>
                    <el-form-item label="Slug" label-position="top">
                        <el-input v-model="form.slug" placeholder="Ссылка для поиска"/>
                    </el-form-item>
                    <el-form-item label="Alt для фото" label-position="top">
                        <el-input v-model="form.alt" placeholder="Напишите Alt для SEO"/>
                    </el-form-item>
                    <el-form-item label="Заголовок" label-position="top">
                        <el-input v-model="form.title" placeholder="Заголовок"/>
                    </el-form-item>
                    <el-form-item label="Описание" label-position="top">
                        <el-input v-model="form.description" placeholder="Описание" type="textarea" :rows="3"/>
                    </el-form-item>
                    <el-button type="primary" @click="onSubmit">Сохранить</el-button>
                    <span v-if="dialogSave" class="text-lime-500 text-sm ml-3">Сохранено</span>
                </el-form>
                <div class="mt-5">
                    <el-input v-model="dialogImageUrl" readonly/>
                    <el-button id="copy_buffer" type="success" class="text-sm mt-2" @click="copyBuffer" plain>
                        Скопировать Url
                    </el-button>
                    <span v-if="dialogCopy" class="text-lime-500 text-sm ml-3">Скопировано</span>
                </div>
            </div>
        </div>
    </el-dialog>
    <DeleteEntityModal name_entity="Изображение" name="gallery_image"/>
</template>

<script setup lang="ts">
import {inject, reactive, ref} from 'vue'
import { Plus } from '@element-plus/icons-vue'
import {Head, router} from "@inertiajs/vue3";
import type { UploadProps, UploadUserFile, UploadRawFile  } from 'element-plus'

import GalleryInfo from './Block/Info.vue'
const props = defineProps({
    gallery: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Карточка Галереи',
    },
});
const dialogImageUrl = ref('')
const dialogVisible = ref(false)
const dialogCopy = ref(false)
const dialogSave = ref(false)
const $delete_entity = inject("$delete_entity")
const form = reactive({
    photo_id: null,
    slug: null,
    alt: null,
    title: null,
    description: null,
})
const fileList = ref<UploadUserFile[]>(props.gallery.photos);
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
const handleRemove: UploadProps['onRemove'] = (uploadFile) => {
    $delete_entity.show(
        route('admin.page.gallery.image-del', {photo: uploadFile.id}),
        {
            name: 'gallery_image',
            state: true
        }
    ).then(response => {
        let index = fileList.value.map(item => item.id).indexOf(uploadFile.id)
        fileList.value.splice(index, 1)
    });
    return false;
}
const handlePictureCardPreview: UploadProps['onPreview'] = (uploadFile) => {
    dialogImageUrl.value = uploadFile.url!
    form.photo_id = uploadFile.id;
    form.alt = uploadFile.alt;
    form.slug = uploadFile.slug;
    form.title = uploadFile.title;
    form.description = uploadFile.description;
    dialogVisible.value = true
}
function onSubmit() {
    dialogSave.value = true;
    setTimeout(() => {
        dialogSave.value = false;
    }, 2000);
    router.post(route('admin.page.gallery.image-set', {photo: form.photo_id}), form);
}

function onSuccess(response, uploadFile, uploadFiles) {
    let index = fileList.value.length - 1
    fileList.value[index].id = response.id
    fileList.value[index].url = response.url
}

function copyBuffer(val) {
    dialogCopy.value = true;
    setTimeout(() => {
        dialogCopy.value = false;
    }, 2000);
    navigator.clipboard.writeText(dialogImageUrl.value);
}
</script>

