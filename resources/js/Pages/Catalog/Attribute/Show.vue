<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Атрибут {{ attribute.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <AttributeInfo :attribute="attribute"/>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'

import { defineProps } from 'vue'
import {Head} from "@inertiajs/vue3";
import AttributeInfo from "./Block/Info.vue";

const props = defineProps({
    attribute: Object,
    title: {
        type: String,
        default: 'Карточка атрибута',
    },
})

</script>

<style scoped>

</style>
