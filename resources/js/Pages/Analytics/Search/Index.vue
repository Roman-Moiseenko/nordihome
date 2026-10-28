<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Поиск</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.searches" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Всего поисков</div>
                <div class="text-2xl font-semibold mt-1">{{ report.totals.searches }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">С кликом по результату</div>
                <div class="text-2xl font-semibold mt-1">{{ report.totals.with_clicks }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Средняя позиция клика</div>
                <div class="text-2xl font-semibold mt-1">{{ report.totals.avg_click_position ?? '—' }}</div>
            </div>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">Топ запросов</div>
            <el-table :data="report.top" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="query" label="Запрос" show-overflow-tooltip />
                <el-table-column prop="searches" label="Поисков" width="100" align="center" />
                <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                <el-table-column prop="clicks" label="Кликов" width="100" align="center" />
                <el-table-column prop="ctr" label="CTR, %" width="100" align="center" />
                <el-table-column prop="avg_position" label="Ср. позиция" width="120" align="center" />
            </el-table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Запросы без результатов</div>
                <el-table :data="report.no_results" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="query" label="Запрос" show-overflow-tooltip />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Запросы с низким CTR</div>
                <el-table :data="report.low_ctr" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="query" label="Запрос" show-overflow-tooltip />
                    <el-table-column prop="searches" label="Поисков" width="100" align="center" />
                    <el-table-column prop="ctr" label="CTR, %" width="100" align="center" />
                </el-table>
            </div>
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
    title: {type: String, default: 'Поиск'},
})
</script>
