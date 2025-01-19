<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Тест NB Russia</h1>
        <el-button @click="test">Тест</el-button>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import { defineProps } from 'vue'
import axios from "axios";

const props = defineProps({

    title: {
        default: 'Тестим темы',
        type: String,
    }
})

function test() {
    router.post('admin.nbrussia.test')
}

</script>

<style scoped>

</style>
