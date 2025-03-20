<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Возврат поставщику {{ refund.number }}
            <span v-if="refund.incoming_number">({{ refund.incoming_number }})</span>
            от {{ func.date(refund.created_at) }}
            <el-tag v-if="refund.trashed" type="danger">Удален</el-tag>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <RefundInfo :refund="refund" :storages="storages" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <RefundActions :refund="refund" />
            </div>
        </el-affix>
        <el-table :data="[...refund.products.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="classes.TableCostCurrency"
                  style="width: 100%;">
            <el-table-column type="index" :index="indexMethod" label="п/п"/>
            <el-table-column prop="product.code" label="Артикул" width="160" />
            <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
            <el-table-column prop="cost_currency" label="Закупочная" width="180">
                <template #default="scope">
                    <el-input v-model="scope.row.cost_currency"
                              :formatter="(value) => func.MaskFloat(value)"
                              @change="setItem(scope.row)"
                              :disabled="iSaving"
                              :readonly="true"
                    >
                        <template #append>{{ refund.currency }}</template>
                    </el-input>
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
                        <template #append>шт</template>
                    </el-input>
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Сумма в валюте" width="180">
                <template #default="scope">
                    {{ func.price(scope.row.quantity * scope.row.cost_currency, refund.currency) }}
                </template>
            </el-table-column>

            <el-table-column label="Действия" align="right" width="180">
                <template #default="scope">
                    <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            :current_page="refund.products.current_page"
            :per_page="refund.products.per_page"
            :total="refund.products.total"
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
import RefundInfo from './Blocks/Info.vue'
import RefundActions from './Blocks/Actions.vue'
import {classes} from "@Res/className"

const props = defineProps({
    refund: Object,
    title: {
        type: String,
        default: 'Возврат поставщику',
    },
    storages: Array,
    operations: Array,
    printed: Object,
    filters: Array,
})
provide('$filters', props.filters) //Фильтр товаров в списке документа
provide('$printed', props.printed) //Для печати
provide('$accounting', props.refund) //Для общих действий

const iSaving = ref(false)
const isEdit = computed<Boolean>(() => !props.refund.completed && !props.refund.trashed);
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSaving.value = true;
    router.visit(route('admin.accounting.refund.set-product', {product: row.id}), {
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
    router.delete(route('admin.accounting.refund.del-product', {product: row.id}))
    //$delete_entity.show(route('admin.accounting.refund.del-product', {product: row.id}));
}
const indexMethod = (index: number) => {
    return index + (props.refund.products.current_page - 1) * props.refund.products.per_page + 1
}
</script>
