<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категория {{ category.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <CategoryInfo :category="category" :categories="categories" />
        </div>
        <el-tabs>
            <PanelChildren :category="category" />
            <PanelAttributes :category="category" />
            <PanelProducts :category="category" />
        </el-tabs>
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import CategoryInfo from "./Block/Info.vue";


import PanelChildren from './Panels/Children.vue'
import PanelAttributes from './Panels/Attributes.vue'
import PanelProducts from  './Panels/Products.vue'

const props = defineProps({
    category: Object,
    categories: Array,
    title: {
        type: String,
        default: 'Карточка категории',
    },
})

</script>

<style scoped>

</style>
