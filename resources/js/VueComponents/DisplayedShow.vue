<template>
    <el-descriptions :column="2" border title="Данные для публикуемой страницы" class="mt-3">
    <el-descriptions-item label="Страница (H1)">{{ displayed.meta.h1 }}</el-descriptions-item>
    <el-descriptions-item label="Заголовок (meta)">{{ displayed.meta.title }}</el-descriptions-item>
    <el-descriptions-item label="Описание (meta)">{{ displayed.meta.description }}</el-descriptions-item>
    <el-descriptions-item label="Фон для секции">
        <div class="" style="height: 32px; display: flex;width: auto;">
            <el-image
                :src="selectedPhoto"
                :zoom-rate="1.2"
                :max-scale="1"
                :min-scale="0.2"
                :preview-src-list="[selectedPhoto]"
                :initial-index="0"
                fit="cover"
            />
        </div>
    </el-descriptions-item>
    <el-descriptions-item label="Заголовок секции">{{ displayed.breadcrumb.caption }}</el-descriptions-item>
    <el-descriptions-item label="Доп.описание секции">{{ displayed.breadcrumb.description }}</el-descriptions-item>
    <el-descriptions-item label="Font Awesome">{{ displayed.awesome }}</el-descriptions-item>
    </el-descriptions>
</template>

<script lang="ts" setup>
import {defineProps, ref} from 'vue'
import axios from "axios";
const urlPhoto = '/admin/page/gallery/get-photo'

const props = defineProps({
    displayed: Object,
});
const selectedPhoto = ref(null)

setPhoto(props.displayed.breadcrumb.photo_id)

function setPhoto(val) {
    if (props === null) return;
    axios.post(urlPhoto, {photo_id: val})
        .then(response => {
            console.log(response.data);
            if (response.data === false) {
                selectedPhoto.value = null;
            } else {
                selectedPhoto.value = response.data;
            }
        });
}

</script>

