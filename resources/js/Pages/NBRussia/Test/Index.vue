<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Тест NB Russia</h1>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
import { defineProps } from 'vue'

const props = defineProps({

    title: {
        default: 'Тестим темы',
        type: String,
    }
})
</script>

<style scoped>

</style>
