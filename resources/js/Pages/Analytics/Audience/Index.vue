<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Аудитория</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.audience" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Новые vs вернувшиеся</div>
                <Chart type="doughnut" :labels="newVsReturningLabels" :datasets="newVsReturningDatasets" :height="280" />
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Устройства</div>
                <Chart type="doughnut" :labels="deviceLabels" :datasets="deviceDatasets" :height="280" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Города</div>
                <el-table :data="report.cities" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Город" />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Регионы</div>
                <el-table :data="report.regions" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Регион" />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Страны</div>
                <el-table :data="report.countries" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Страна" />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
                </el-table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Операционные системы</div>
                <el-table :data="report.os" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="ОС" />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Браузеры</div>
                <el-table :data="report.browsers" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Браузер" />
                    <el-table-column prop="total" label="Кол-во" width="100" align="center" />
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
    title: {type: String, default: 'Аудитория'},
})

const newVsReturningLabels = computed(() => (props.report.new_vs_returning ?? []).map(i => i.name === 'new' ? 'Новые' : 'Вернувшиеся'))
const newVsReturningDatasets = computed(() => [{
    data: (props.report.new_vs_returning ?? []).map(i => i.total),
    backgroundColor: ['#134e4a', '#d97706'],
}])

const deviceLabels = computed(() => (props.report.devices ?? []).map(i => i.name))
const deviceDatasets = computed(() => [{
    data: (props.report.devices ?? []).map(i => i.total),
    backgroundColor: ['#134e4a', '#d97706', '#64748b', '#94a3b8'],
}])
</script>
