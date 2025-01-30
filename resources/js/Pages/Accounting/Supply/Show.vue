<template>

    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Заказ поставщику {{ supply.number }} <span v-if="supply.incoming_number">({{
                supply.incoming_number
            }})</span> от {{ func.date(supply.created_at) }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <SupplyInfo :supply="supply" :customers="customers"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <SupplyActions :supply="supply"/>
            </div>
        </el-affix>
        <el-table :data="tableDate"
                  header-cell-class-name="nordihome-header"
                  style="width: 100%;"
                  :row-class-name="tableRowClassName"
        >
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160"/>
            <el-table-column prop="product.name" label="Товар">
                <template #default="scope">
                    <ProductRename :product="scope.row.product"/>
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Кол-во" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.quantity"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                    >
                        <template #append>шт</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="cost_currency" label="Закупочная" width="280" align="center">
                <template #default="scope">
                    <div class="flex items-center">
                        <el-tag size="large" :type="classCost(scope.row)" effect="light">
                            {{ func.price(scope.row.pre_cost, supply.currency) }}
                        </el-tag>
                        <el-input v-model="scope.row.cost_currency"
                                  :formatter="(value) => func.MaskFloat(value)"
                                  @change="setItem(scope.row)"
                                  :disabled="iSaving"
                                  :readonly="!isEdit"
                                  class="ml-2"
                                  style="width: 160px"
                        >
                            <template #append>{{ supply.currency }}</template>
                        </el-input>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="Сумма в валюте" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.amount" :formatter="val => func.MaskFloat(val, 2)"
                              @change="setAmount(scope.row)"
                              :disabled="iSaving"
                              :readonly="!isEdit"
                    >
                        <template #append>{{ supply.currency }}</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column label="Сумма в рублях" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost_currency * supply.exchange_fix) }}
                </template>
            </el-table-column>
            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button type="danger" @click="handleDeleteEntity(scope.row)" plain>
                        <el-icon>
                            <Delete/>
                        </el-icon>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="supply.products.current_page"
            :per_page="supply.products.per_page"
            :total="supply.products.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из заказа"/>

</template>

<script lang="ts" setup>
import {inject, ref, defineProps, computed, provide} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import SupplyInfo from './Blocks/Info.vue'
import SupplyActions from './Blocks/Actions.vue'
import Pagination from '@Comp/Pagination.vue'
import ProductRename from "@Comp/Product/Rename.vue";

const props = defineProps({
    supply: Object,
    title: {
        type: String,
        default: 'Заказ поставщику',
    },
    printed: Object,
    filters: Array,
    customers: Array,
})
provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.supply) //Для общих действий
const tableDate = [...props.supply.products.data.map(item => {
    item.amount = (item.quantity * item.cost_currency).toFixed(2)
    return item
})]
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
const isEdit = computed<Boolean>(() => !props.supply.completed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.supply.set-product', {product: row.id}), {
        method: "post",
        data: {
            quantity: row.quantity,
            cost: row.cost_currency
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function setAmount(row) {
    row.cost_currency = row.amount / row.quantity
    setItem(row)
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.supply.del-product', {product: row.id}));
}
function classCost(row) {
    if (row.pre_cost > row.cost_currency) return 'success'
    if (row.pre_cost < row.cost_currency) return 'danger'
    return 'info';
}
const indexMethod = (index: number) => {
    return index + (props.supply.products.current_page - 1) * props.supply.products.per_page + 1
}
</script>
