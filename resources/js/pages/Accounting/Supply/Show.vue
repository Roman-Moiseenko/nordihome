<template>
    <Layout>
        <el-config-provider :locale="ru">
            <Head><title>{{ title }}</title></Head>
            <h1 class="font-medium text-xl">
                Заказ поставщику {{ supply.number }} <span v-if="supply.incoming_number">({{ supply.incoming_number }})</span> от {{ func.date(supply.created_at) }}
            </h1>
            <div class="mt-3 p-3 bg-white rounded-lg ">
                <SupplyInfo :supply="supply" />
            </div>
            <el-affix target=".affix-container" :offset="64">
                <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                    <SupplyActions :supply="supply" />
                </div>
            </el-affix>


            <el-table :data="[...supply.products]"
                      header-cell-class-name="nordihome-header"
                      style="width: 100%;">
                <el-table-column prop="product.code" label="Артикул" width="160" />
                <el-table-column prop="product.name" label="Товар" show-overflow-tooltip/>
                <el-table-column prop="cost_currency" label="Закупочная" width="180">
                    <template #default="scope">
                        <el-input v-model="scope.row.cost_currency"
                                  :formatter="(value) => func.MaskFloat(value)"
                                  @change="setItem(scope.row)"
                                  :disabled="iSaving"
                                  :readonly="!isEdit"
                        >
                            <template #append>{{ supply.currency }}</template>
                        </el-input>
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Кол-во" width="180">
                    <template #default="scope">
                        <el-input v-model="scope.row.quantity"
                                  :formatter="(value) => func.MaskInteger(value)"
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
                        {{ func.price(scope.row.quantity * scope.row.cost_currency, supply.currency) }}
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Сумма в рублях" width="180">
                    <template #default="scope">
                        {{ func.price(scope.row.quantity * scope.row.cost_currency * supply.exchange_fix) }}
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right" width="180">
                    <template #default="scope">
                        <el-button v-if="isEdit" type="danger" @click="handleDeleteEntity(scope.row)" plain><el-icon><Delete /></el-icon></el-button>
                    </template>
                </el-table-column>

            </el-table>
        </el-config-provider>
        <DeleteEntityModal name_entity="Товар из заказа" />
    </Layout>
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps, computed} from "vue";
import Layout from "@Comp/Layout.vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'

import SupplyInfo from './Blocks/Info.vue'
import SupplyActions from './Blocks/Actions.vue'

const props = defineProps({
    supply: Object,
    title: {
        type: String,
        default: 'Заказы поставщикам',
    },
})

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
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.supply.del-product', {product: row.id}));
}
</script>
