<template>
    <Head><title>Настройки Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Настройки Авито</h1>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
</script>
