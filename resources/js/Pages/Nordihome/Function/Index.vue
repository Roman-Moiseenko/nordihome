<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Тест Норди хоум</h1>

        <el-tabs tab-position="top" type="border-card">
            <ParserDimensions />
            <ParserCategory />
        </el-tabs>

    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
import { defineProps } from 'vue'
import ParserDimensions from "./Panels/ParserDimensions.vue";
import ParserCategory from "./Panels/ParserCategory.vue";

const props = defineProps({

    title: {
        default: 'Тестим темы',
        type: String,
    }
})
</script>

<style scoped>

</style>
