<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <h2 class="font-medium">Водянной знак</h2>
                    <el-form-item label="Путь к файлу /images/шаблон/..." label-position="left">
                        <el-input v-model="form.watermark_file" style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="От размера изображения" label-position="left">
                        <el-input v-model="form.watermark_size" :formatter="val => func.MaskFloat(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Расположение" label-position="left">
                        <el-input v-model="form.watermark_position" style="width: 300px;"/>
                    </el-form-item>

                    <el-form-item label="Смещение" label-position="left">
                        <el-input v-model="form.watermark_offset" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <h2 class="font-medium">Образы</h2>
                    <div v-for="(thumb, index) in form.thumbs">
                        <el-form-item label="Название slug" label-position="left">
                            <el-input v-model="thumb.name" />
                        </el-form-item>
                        <el-form-item label="Ширина" label-position="left">
                            <el-input v-model="thumb.width" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                        </el-form-item>
                        <el-form-item label="Высота" label-position="left">
                            <el-input v-model="thumb.height" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                        </el-form-item>
                        <el-form-item label="Обрезка" label-position="left">
                            <el-checkbox v-model="thumb.fit" :checked="form.fit"/>
                        </el-form-item>
                        <el-form-item label="Водяной знак" label-position="left">
                            <el-checkbox v-model="thumb.watermark" :checked="form.watermark"/>
                        </el-form-item>
                        <el-button>Удалить</el-button>
                    </div>
                    <el-button>Добавить</el-button>


                </el-col>
                <el-col :span="8">


                </el-col>
            </el-row>

            <el-button type="primary" @click="onSubmit">Сохранить</el-button>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import {defineProps, reactive} from "vue";
import {func} from '@Res/func.js'

const props = defineProps({
    image: Object,
    title: {
        type: String,
        default: 'Изображения на сайт',
    },
})

const form = reactive({
    slug: 'image',
    watermark_file: props.image.watermark_file,
    watermark_size: props.image.watermark_size,
    watermark_position: props.image.watermark_position,
    watermark_offset: props.image.watermark_offset,
    createThumbsOnSave: props.image.createThumbsOnSave,
    createThumbsOnRequest: props.image.createThumbsOnRequest,
    thumbs: [...props.image.thumbs],


})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
