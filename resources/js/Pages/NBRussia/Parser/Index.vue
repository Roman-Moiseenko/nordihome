<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Парсинг New Balance</h1>
        <el-button @click="test">Категории</el-button>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import { defineProps } from 'vue'
import axios from "axios";

const props = defineProps({

    title: {
        default: 'Парсинг New Balance',
        type: String,
    }
})

function test() {
   // router.post(route('admin.nbrussia.test'))
    axios.post(route('admin.nbrussia.parser.categories')).then(response => {
        console.log(response.data)
    })

}

</script>

<style scoped>

</style>
