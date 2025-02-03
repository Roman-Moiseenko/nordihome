<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Заказы на доставку</h1>
        <div class="mt-2 p-5 bg-white rounded-md">
            <h2 class="font-medium text-green-600">Озон</h2>
            <OzonTable :ozon="ozon" :drivers="drivers" />
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <h2 class="font-medium text-teal-600">По региону</h2>
            <LocalTable :local="local" :assembles="assembles" :drivers="drivers" />
            <h2 v-if="incomplete.length > 0" class="mt-3 font-medium text-red-600">Не завершенные</h2>
            <LocalTable v-if="incomplete.length > 0" :local="incomplete" :assembles="assembles" :drivers="drivers" :is_incomplete="true" />
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <h2 class="font-medium text-slate-600">По РФ (Почта, ТК)</h2>
            <RegionTable :region="region" :drivers="drivers" :companies="cargo_companies"/>
        </div>

    </el-config-provider>

</template>
<script lang="ts" setup>
import { defineProps } from "vue";
import {Head } from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'

import LocalTable from './Local/Table.vue'
import RegionTable from './Region/Table.vue'
import OzonTable from './Ozon/Table.vue'


const props = defineProps({
    local: Array,
    incomplete: Array,
    region: Array,
    ozon: Array,
    calendar: Object,
    title: {
        type: String,
        default: 'Заказы на доставку',
    },
    drivers: Array,
    assembles: Array,
    cargo_companies: Array,
})

</script>

<style scoped>

</style>
