<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Возврат товаров № {{ refund.number }} от {{ func.date(refund.created_at) }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <RefundInfo :refund="refund" :reasons="reasons"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <RefundActions :refund="refund"/>
            </div>
        </el-affix>
        <div class="mt-1 px-3 py-1 bg-white rounded-md">
            <el-table
                :data="[...refund.items]"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column type="index" label="п/п"/>
                <el-table-column prop="product.code" label="Артикул" width="110"/>
                <el-table-column prop="product.name" label="Товар" width="320" show-overflow-tooltip/>
                <el-table-column prop="sell_cost" label="Цена продажи" width="180" align="center">
                    <template #default="scope">
                        {{ func.price(scope.row.sell_cost)}}
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Кол-во">
                    <template #default="scope">
                        <el-tag v-if="scope.row.completed" type="info">{{ scope.row.quantity}}</el-tag>
                        <div v-else class="flex">
                            <el-input v-model="scope.row.quantity"  @change="setItem(scope.row)" style="width: 120px;" :disabled="disabled"/>
                            <el-button type="danger" @click="delItem(scope.row)"  :disabled="disabled">
                                <i class="fa-light fa-trash"></i>
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>

            <el-table v-if="refund.additions.length > 0"
                :data="[...refund.additions]"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                class="mt-2"
            >
                <el-table-column type="index" label="п/п"/>
                <el-table-column prop="addition.name" label="Услуга" width="240" show-overflow-tooltip/>
                <el-table-column prop="amount" label="Сумма" >
                    <template #default="scope">
                        <el-tag v-if="scope.row.completed" type="info">{{ scope.row.amount}}</el-tag>
                        <div v-else class="flex">
                            <el-input v-model="scope.row.amount"  @change="setAddition(scope.row)"  :disabled="disabled" style="width: 120px;"/>
                            <el-button type="danger" @click="delAddition(scope.row)"  :disabled="disabled">
                                <i class="fa-light fa-trash"></i>
                            </el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из возврата" name="product"/>
    <DeleteEntityModal name_entity="Услугу из возврата" name="addition"/>
</template>

<script setup lang="ts">
import {Head, Link, router} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import {computed, defineProps, inject, ref} from "vue";
import RefundInfo from "./Blocks/Info.vue"
import RefundActions from "./Blocks/Actions.vue"
import { func } from  "@Res/func.js"

const props = defineProps({
    refund: Object,
    title: {
        type: String,
        default: 'Возврат товаров',
    },
    reasons: Array,
})
const iSavingInfo = ref(false)

const disabled = computed(() => {
    return iSavingInfo.value || props.refund.completed !== 0
})
const $delete_entity = inject("$delete_entity")

function setItem(row) {
    iSavingInfo.value = true
    router.visit(route('admin.order.refund.set-item', {item: row.id}), {
        method: "post",
        data: {quantity: row.quantity,},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
function setAddition(row) {
    iSavingInfo.value = true
    router.visit(route('admin.order.refund.set-ad' +
        'dition', {addition: row.id}), {
        method: "post",
        data: {amount: row.amount,},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
function delItem(row) {
    $delete_entity.show(route('admin.order.refund.del-item', {item: row.id}), 'product');
}
function delAddition(row) {
    $delete_entity.show(route('admin.order.refund.del-addition', {item: row.id}), 'addition');
}
</script>


<style scoped>

</style>
