<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Каталог {{ widget.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <CatalogInfo :widget="widget" :templates="templates"/>
        </div>

     <el-button>Добавить</el-button>

        <div class="mt-3 p-3 bg-white rounded-lg ">
            <CatalogItems :items="widget.items" />
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

import CatalogInfo from './Block/Info.vue'
import CatalogItems from './Block/Items.vue'

const props = defineProps({
    widget: Object,
    templates: Array,
    title: {
        type: String,
        default: 'Карточка каталога',
    },
})
const form = reactive({
    modelId: null,
    modelType: null,
    caption: null,
    description: null,
})
function onAddItem(val) {
    router.visit(route('admin.content.widget.catalog.add-item', {widget: props.widget.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}
</script>
