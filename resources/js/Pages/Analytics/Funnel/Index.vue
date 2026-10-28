<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Воронка продаж</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.funnel" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Просмотры страниц</div>
                <div class="text-2xl font-semibold mt-1">{{ stage('page_view') }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Добавления в корзину</div>
                <div class="text-2xl font-semibold mt-1">{{ stage('cart_add') }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Оформленные заказы</div>
                <div class="text-2xl font-semibold mt-1">{{ stage('order_placed') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Воронка (сессии)</div>
                <Chart type="bar" :labels="funnelLabels" :datasets="funnelDatasets" :height="300" />
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Брошенные корзины</div>
                <div class="text-2xl font-semibold">{{ report.abandoned_carts }}</div>
                <div class="text-sm text-slate-500 mt-1">сессий с добавлением в корзину, но без заказа</div>
            </div>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">Топ путей клиента</div>
            <el-table :data="report.top_paths" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="path" label="Путь" show-overflow-tooltip />
                <el-table-column prop="count" label="Сессий" width="120" align="center" />
            </el-table>
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
    title: {type: String, default: 'Воронка продаж'},
})

function stage(name) {
    const found = (props.report.stages ?? []).find(s => s.stage === name)
    return found ? found.sessions : 0
}

const funnelLabels = computed(() => (props.report.stages ?? []).map(s => {
    const map = {page_view: 'Просмотр', cart_add: 'Корзина', order_placed: 'Заказ'}
    return map[s.stage] ?? s.stage
}))
const funnelDatasets = computed(() => [{
    label: 'Сессии',
    data: (props.report.stages ?? []).map(s => s.sessions),
    backgroundColor: ['#134e4a', '#d97706', '#0f766e'],
}])
</script>
