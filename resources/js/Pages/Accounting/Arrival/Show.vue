<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Приходная накладная {{ arrival.number }} <span v-if="arrival.incoming_number">({{ arrival.incoming_number }})</span> от {{ func.date(arrival.created_at) }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ArrivalInfo :arrival="arrival" :storages="storages" :operations="operations" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <ArrivalActions :arrival="arrival" />
            </div>
        </el-affix>
        <el-table :data="[...arrival.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="tableRowClassName"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160" />
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip>
                <template #default="scope">
                    <ProductRename :product="scope.row.product" />
                </template>
            </el-table-column>
            <el-table-column prop="cost_currency" label="Цена" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.cost_currency"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit || arrival.supply_id"
                    >
                        <template #append>{{ arrival.currency }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="product.pre_cost" label="Предыдущая" width="120" align="center">
                <template #default="scope">
                        {{ func.price(scope.row.pre_cost, arrival.currency) }}
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Кол-во" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.quantity"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    >
                        <template #append>{{ scope.row.measuring }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Сумма в валюте" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost_currency, arrival.currency) }}
                </template>
            </el-table-column>
            <el-table-column label="Сумма в рублях" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost_currency * arrival.exchange_fix) }}
                </template>
            </el-table-column>
            <el-table-column v-if="isEdit" label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
            <el-table-column v-if="!isEdit"  prop="remains" label="Остаток" align="right" width="180" >
                <template #default="scope">
                {{ parseFloat(scope.row.remains) }} {{ scope.row.measuring }}
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="arrival.products.current_page"
            :per_page="arrival.products.per_page"
            :total="arrival.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из поступления" />
</template>

<script lang="ts" setup>
import {inject, ref, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import ArrivalInfo from './Blocks/Info.vue'
import ArrivalActions from './Blocks/Actions.vue'
import ProductRename from "@Comp/Product/Rename.vue"

const props = defineProps({
    arrival: Object,
    title: {
        type: String,
        default: 'Поступление товаров (приходная накладная)',
    },
    storages: Array,
    operations: Array,
    printed: Object,
    filters: Array,
})

provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.arrival) //Для общих действий
interface IRow {
    cost_currency: number,
    quantity: number,
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.cost_currency === 0 || row.quantity === 0) {
        return 'error-row'
    }
    return ''
}
const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.arrival.completed && !props.arrival.trashed);
const $delete_entity = inject("$delete_entity")
function setItem(row: any) {
    iSaving.value = true;
    router.visit(route('admin.accounting.arrival.set-product', {product: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            cost: row.cost_currency
        },
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.arrival.del-product', {product: row.id}));
}

const indexMethod = (index: number) => {
    return index + (props.arrival.products.current_page - 1) * props.arrival.products.per_page + 1
}
</script>
