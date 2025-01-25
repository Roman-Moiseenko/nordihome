<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">

        <el-tab-pane>
            <template #label>Парсер Категории</template>
            <el-button @click="categories" class="mt-2">Категории</el-button>
        </el-tab-pane>


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

function categories() {
//    router.post(route('admin.nordihome.parser.categories'))
  //  return;
    axios.post(route('admin.nordihome.parser.categories')).then(response => {
        console.log(response.data)
    })

}

</script>

<style scoped>

</style>
