<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Виджет {{ widget.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PostInfo :widget="widget" :templates="templates"/>
        </div>

        <div class="mt-3 p-3 bg-white rounded-lg ">
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import PostInfo from './Block/Info.vue'

const props = defineProps({
    widget: Object,
    templates: Array,
    title: {
        type: String,
        default: 'Карточка виджета',
    },
})

</script>
