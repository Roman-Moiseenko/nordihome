<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Просмотры страниц</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.pages" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Просмотров</div>
                <div class="text-2xl font-semibold mt-1">{{ report.metrics.views }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Ср. время на странице, сек</div>
                <div class="text-2xl font-semibold mt-1">{{ report.metrics.avg_duration ?? '—' }}</div>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500">Ср. глубина прокрутки, %</div>
                <div class="text-2xl font-semibold mt-1">{{ report.metrics.avg_scroll_depth ?? '—' }}</div>
            </div>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">Топ товаров по просмотрам</div>
            <el-table :data="report.top_products" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="name" label="Товар" show-overflow-tooltip />
                <el-table-column prop="views" label="Просмотров" width="120" align="center" />
                <el-table-column prop="unique_visitors" label="Посетители" width="130" align="center" />
                <el-table-column prop="avg_duration" label="Ср. время" width="120" align="center" />
                <el-table-column prop="avg_scroll_depth" label="Ср. прокрутка" width="130" align="center" />
            </el-table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Топ категорий</div>
                <el-table :data="report.top_categories" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Категория" show-overflow-tooltip />
                    <el-table-column prop="views" label="Просмотров" width="110" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Топ статей</div>
                <el-table :data="report.top_posts" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="name" label="Статья" show-overflow-tooltip />
                    <el-table-column prop="views" label="Просмотров" width="110" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Страницы с отказами</div>
                <el-table :data="report.high_bounce" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="page_type" label="Тип" width="110" />
                    <el-table-column prop="entity_id" label="ID" width="80" align="center" />
                    <el-table-column prop="bounces" label="Отказов" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Точки входа</div>
                <el-table :data="report.entry_pages" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="page_type" label="Тип" width="110" />
                    <el-table-column prop="entity_id" label="ID" width="80" align="center" />
                    <el-table-column prop="total" label="Входов" width="100" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Точки выхода</div>
                <el-table :data="report.exit_pages" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="page_type" label="Тип" width="110" />
                    <el-table-column prop="entity_id" label="ID" width="80" align="center" />
                    <el-table-column prop="total" label="Выходов" width="100" align="center" />
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
    title: {type: String, default: 'Просмотры страниц'},
})
</script>
