<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Поставщик {{ distributor.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg">
            <DistributorInfo :distributor="distributor" :organizations="organizations"  />
        </div>
        <div class="mt-3">
            <Link type="primary" :href="route('admin.accounting.distributor.show', {distributor: distributor.id})">Все</Link> ({{ count.all }}) |
            <Link type="primary" :href="route('admin.accounting.distributor.show', {distributor: distributor.id, balance: 'min'})">Минимальный</Link> ({{ count.min }}) |
            <Link type="primary" :href="route('admin.accounting.distributor.show', {distributor: distributor.id, balance: 'empty'})">Отсутствующий</Link> ({{ count.empty }}) |
            <Link type="primary" :href="route('admin.accounting.distributor.show', {distributor: distributor.id, balance: 'no_buy'})">Не заказывать</Link> ({{ count.no_buy }})
        </div>
        <el-table :data="[...products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="tableRowClassName"
                  style="width: 100%;">
            <el-table-column type="index" :index="throughIndex" width="40" />
            <el-table-column prop="code" label="Артикул" width="160" />
            <el-table-column prop="name" label="Товар" show-overflow-toolti>
                <template #default="scope">
                    <Link type="info" :href="route('admin.product.edit', {product: scope.row.id})">{{ scope.row.name }}</Link>
                </template>
            </el-table-column>
            <el-table-column prop="" label="Закупочная" width="180">
                <template #default="scope">
                    <span :class="classCostColor(scope.row)">
                        <span class="font-medium">
                        {{ func.price(scope.row.cost, distributor.currency.sign) }}
                        </span>
                    / {{ func.price(scope.row.pre_cost, distributor.currency.sign) }}
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Наличие (-Резерв)" width="180">
                <template #default="scope">
                    {{ scope.row.quantity }} <span v-if="scope.row.reserve > 0"> (- {{ scope.row.reserve }})</span>
                </template>
            </el-table-column>
            <el-table-column label="Баланс" width="180">
                <template #default="scope">
                    {{ scope.row.balance.min }} <span v-if="scope.row.balance.max"> / {{ scope.row.balance.max }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="price_retail" label="Цена продажи" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.price_retail) }}
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />

    </el-config-provider>
</template>

<script lang="ts" setup>
import {inject, ref, defineProps, provide} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import DistributorInfo from "./Blocks/Info.vue";

const props = defineProps({
    distributor: Object,
    organizations: Array,
    products: Object,
    title: {
        type: String,
        default: 'Карточка поставщика',
    },
    count: Array,
})

interface IRow {
    balance: number,
    quantity: number,
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.balance.buy === false) return 'gray-row' //Не заказывать серый
    if (row.quantity === 0) return 'error-row' //Равно нулю - красный
    if (row.quantity < row.balance.min) return 'warning-row'  //Меньше баланса - оранж
    return ''
}
function classCostColor(row) {
    if (!row.pre_cost) return ''
    if (row.cost > row.pre_cost) return 'text-red-600'
    if (row.cost < row.pre_cost) return 'text-green-600'
    return ''
}
function throughIndex(index) {
    return index + (props.products.current_page - 1) * props.products.per_page + 1
}
</script>
