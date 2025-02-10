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
                <el-col :span="16">
                    <h2 class="font-medium">Образы</h2>

                    <el-table
                        :data="[...form.thumbs]"
                        header-cell-class-name="nordihome-header"
                    >
                        <el-table-column prop="name" label="Название" width="120">
                            <template #default="scope">
                                <el-input v-model="scope.row.name" />
                            </template>
                        </el-table-column>

                        <el-table-column prop="width" label="Ширина" width="100">
                            <template #default="scope">
                                <el-input v-model="scope.row.width" :formatter="val => func.MaskInteger(val)" />
                            </template>
                        </el-table-column>
                        <el-table-column prop="height" label="Высота" width="100">
                            <template #default="scope">
                                <el-input v-model="scope.row.height" :formatter="val => func.MaskInteger(val)" />
                            </template>
                        </el-table-column>
                        <el-table-column prop="fit" label="Обрезка" width="120">
                            <template #default="scope">
                                <el-checkbox v-model="scope.row.fit"  :checked="scope.row.fit   "/>
                            </template>
                        </el-table-column>
                        <el-table-column prop="watermark" label="Водяной знак" width="120">
                            <template #default="scope">
                                <el-checkbox v-model="scope.row.watermark"  :checked="scope.row.watermark"/>
                            </template>
                        </el-table-column>
                        <el-table-column label="" width="80">
                            <template #default="scope">
                                <el-button type="danger" @click="onDelete(scope.row)"><i class="fa-light fa-trash"></i></el-button>
                            </template>
                        </el-table-column>
                    </el-table>


                    <el-button type="success" @click="onAdd">Добавить</el-button>

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

function onAdd() {
    form.thumbs.push({
        name: 'new-name',
        width: null,
        height: null,
        fit: false,
        watermark: false,
    })
}

function onDelete(row) {
    let index = form.thumbs.map(function (el) {
        return el.name;
    }).indexOf(row.name);
    form.thumbs.splice(index, 1)

}

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
