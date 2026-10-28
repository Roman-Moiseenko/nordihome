<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex items-center">
            <h1 class="font-medium text-xl">Действия клиентов</h1>
            <div class="ml-auto">
                <DateRange :filters="filters" route-name="admin.analytics.actions" />
            </div>
        </div>

        <div class="mt-4 p-5 bg-white rounded-md shadow-sm">
            <div class="text-sm text-slate-500 mb-3">Действия по типам</div>
            <el-table :data="report.totals" header-cell-class-name="nordihome-header" style="width: 100%">
                <el-table-column prop="action_type" label="Действие" />
                <el-table-column prop="total" label="Всего" width="120" align="center" />
                <el-table-column prop="unique_visitors" label="Посетители" width="130" align="center" />
                <el-table-column prop="entities" label="Сущностей" width="120" align="center" />
            </el-table>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Добавления в корзину (товары)</div>
                <el-table :data="report.cart_adds" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="entity_id" label="ID" width="90" align="center" />
                    <el-table-column prop="total" label="Добавлений" width="120" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Удаления из корзины (товары)</div>
                <el-table :data="report.cart_removes" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="entity_id" label="ID" width="90" align="center" />
                    <el-table-column prop="total" label="Удалений" width="120" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Избранное (товары)</div>
                <el-table :data="report.wishlist_adds" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="entity_id" label="ID" width="90" align="center" />
                    <el-table-column prop="total" label="Добавлений" width="120" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
                </el-table>
            </div>
            <div class="p-5 bg-white rounded-md shadow-sm">
                <div class="text-sm text-slate-500 mb-3">Клики по баннерам</div>
                <el-table :data="report.banner_clicks" header-cell-class-name="nordihome-header" style="width: 100%">
                    <el-table-column prop="entity_id" label="ID" width="90" align="center" />
                    <el-table-column prop="total" label="Кликов" width="120" align="center" />
                    <el-table-column prop="unique_visitors" label="Посетители" width="120" align="center" />
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
    title: {type: String, default: 'Действия клиентов'},
})
</script>
