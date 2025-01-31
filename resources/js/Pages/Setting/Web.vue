<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <el-form-item label="Количество товаров на странице" label-position="top">
                        <el-input v-model="form.paginate" :formatter="val => func.MaskInteger(val)"  style="width: 150px;"/>
                    </el-form-item>
                    <el-form-item label="Логотип для сайта, с прозрачным фоном (svg, png)" label-position="top">
                        <el-input v-model="form.logo_img"   style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="Подпись (alt) под логотипом" label-position="top">
                        <el-input v-model="form.logo_alt"   style="width: 300px;"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Кеширование страниц" label-position="left">
                        <el-checkbox v-model="form.is_cache" :checked="form.is_cache"/>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <h2 class="font-medium">SEO-настройки (Заголовки)</h2>
                    <el-form-item label="meta-Title для списка категорий" label-position="top">
                        <el-input v-model="form.categories_title"   style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="meta-Description для списка категорий" label-position="top">
                        <el-input v-model="form.categories_desc"   style="width: 300px;" type="textarea" :rows="3"/>
                    </el-form-item>
                    <el-form-item label="meta-Title Контактные данные" label-position="top">
                        <el-input v-model="form.title_contact"   style="width: 300px;"/>
                    </el-form-item>
                    <el-form-item label="meta-Title Бренд и Город" label-position="top">
                        <el-input v-model="form.title_city"   style="width: 300px;"/>
                    </el-form-item>
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
    web: Object,
    title: {
        type: String,
        default: 'Настройка сайта',
    },
})

const form = reactive({
    slug: 'web',
    paginate: props.web.paginate,
    logo_img: props.web.logo_img,
    logo_alt: props.web.logo_alt,
    categories_title: props.web.categories_title,
    categories_desc: props.web.categories_desc,
    title_contact: props.web.title_contact,
    title_city: props.web.title_city,
    is_cache: props.web.is_cache,
})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>
