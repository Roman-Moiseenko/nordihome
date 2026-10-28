<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Источники и каналы</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.sources" />
            </div>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">Источники трафика</div>
            <el-table :data="report.sources" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="source" label="Источник" />
                <el-table-column prop="sessions" label="Сессии" width="110" align="center" />
                <el-table-column prop="unique_visitors" label="Посетители" width="130" align="center" />
                <el-table-column prop="new_visitors" label="Новые" width="100" align="center" />
                <el-table-column prop="bounces" label="Отказы" width="100" align="center" />
                <el-table-column prop="avg_duration" label="Ср. время" width="110" align="center" />
                <el-table-column prop="conversion" label="Конверсия, %" width="140" align="center" />
            </el-table>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">UTM-кампании</div>
            <el-table :data="report.utm_campaigns" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="campaign" label="Кампания" />
                <el-table-column prop="source" label="Источник" width="180" />
                <el-table-column prop="sessions" label="Сессии" width="120" align="center" />
                <el-table-column prop="visitors" label="Посетители" width="130" align="center" />
            </el-table>
        </div>
    </el-config-provider>
</template>

<script lang="ts" setup>
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import DateRange from '@Page/Analytics/Blocks/DateRange.vue'

defineProps({
    report: {type: Object, required: true},
    filters: {type: Object, required: true},
    title: {type: String, default: 'Источники и каналы'},
})
</script>
