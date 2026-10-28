<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Аналитика — Обзор</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.dashboard" />
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <div v-for="card in cards" :key="card.label" class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">{{ card.label }}</div>
                <div class="text-2xl font-semibold mt-1">{{ card.value }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            <div class="lg:col-span-2 p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Активность по дням</div>
                <Chart type="line" :labels="dailyLabels" :datasets="dailyDatasets" :height="320" />
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Устройства</div>
                <Chart type="doughnut" :labels="deviceLabels" :datasets="deviceDatasets" :height="320" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Источники трафика</div>
                <Chart type="doughnut" :labels="sourceLabels" :datasets="sourceDatasets" :height="300" />
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Источники (таблица)</div>
                <el-table :data="report.sources" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Источник" />
                    <el-table-column prop="sessions" label="Сессии" width="100" align="center" />
                    <el-table-column prop="visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
        </div>
    </el-config-provider>
</template>

<script lang="ts" setup>
import {computed} from 'vue'
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import Chart from '@Comp/Analytics/Chart.vue'
import DateRange from '@Page/Analytics/Blocks/DateRange.vue'

const props = defineProps({
    report: {type: Object, required: true},
    filters: {type: Object, required: true},
    title: {type: String, default: 'Аналитика — Обзор'},
})

const totals = computed(() => props.report.totals ?? {})

const cards = computed(() => [
    {label: 'Посетители', value: totals.value.visitors ?? 0},
    {label: 'Сессии', value: totals.value.sessions ?? 0},
    {label: 'Просмотры', value: totals.value.page_views ?? 0},
    {label: 'Действия', value: totals.value.actions ?? 0},
    {label: 'Поиски', value: totals.value.searches ?? 0},
    {label: 'Отказы, %', value: totals.value.bounce_rate ?? 0},
    {label: 'Ср. длительность, сек', value: totals.value.avg_session_duration ?? '—'},
])

const dailyLabels = computed(() => (props.report.daily ?? []).map(i => i.date))
const dailyDatasets = computed(() => [
    {
        label: 'Сессии',
        data: (props.report.daily ?? []).map(i => i.sessions),
        borderColor: '#134e4a',
        backgroundColor: '#134e4a',
        tension: 0.3,
    },
    {
        label: 'Просмотры',
        data: (props.report.daily ?? []).map(i => i.page_views),
        borderColor: '#d97706',
        backgroundColor: '#d97706',
        tension: 0.3,
    },
])

const deviceLabels = computed(() => (props.report.devices ?? []).map(i => i.name))
const deviceDatasets = computed(() => [{
    data: (props.report.devices ?? []).map(i => i.sessions),
    backgroundColor: ['#134e4a', '#d97706', '#64748b', '#94a3b8'],
}])

const sourceLabels = computed(() => (props.report.sources ?? []).map(i => i.name))
const sourceDatasets = computed(() => [{
    data: (props.report.sources ?? []).map(i => i.sessions),
    backgroundColor: ['#134e4a', '#d97706', '#0f766e', '#c2410c', '#64748b', '#94a3b8', '#475569', '#b45309'],
}])
</script>
