<template>
    <template v-if="arrival.completed">
        <el-dropdown v-if="arrival.distributor_id">
            <el-button type="primary">
                Создать на основании<el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item @click="onMovement">Перемещение запасов</el-dropdown-item>
                    <el-dropdown-item @click="onPricing">Установка цен</el-dropdown-item>
                    <el-dropdown-item @click="onRefund">Возврат поставщику</el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>

        <AccountingPrint :print="print" />
        <AccountingWork v-if="arrival.distributor_id" :route="route('admin.accounting.arrival.work', {arrival: props.arrival.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.arrival.add-product', {arrival: arrival.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.arrival.add-products', {arrival: arrival.id})" class="ml-3"/>
        <el-button type="warning"  class="ml-3" @click="onExpenses">Дополнительные расходы</el-button>
        <AccountingCompleted :route="route('admin.accounting.arrival.completed', {arrival: props.arrival.id})" />
    </template>
    <AccountingOnBased :based="arrival.based" :founded="arrival.founded"/>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(arrival.amount, arrival.currency) }}</el-tag>
        <span v-if="arrival.expense" class="ml-2">
            Доп.расходы <el-tag type="warning" size="large">{{ func.price(arrival.expense.amount) }}</el-tag>
        </span>
    </span>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";
import AccountingCompleted from "@Comp/Pages/AccountingCompleted.vue";
import AccountingWork from "@Comp/Pages/AccountingWork.vue";
import {ElLoading} from "element-plus";

const props = defineProps({
    arrival: Object,
    print: Array,
})

function onExpenses() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.expense', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onMovement() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.movement', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onPricing() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.pricing', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onRefund() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.arrival.refund', {arrival: props.arrival.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}

</script>
