<template>
    <template v-if="supply.completed">
        <el-dropdown>
            <el-button type="primary">
                Создать на основании<el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item @click="onPayment">Платежное поручение</el-dropdown-item>
                    <el-dropdown-item @click="onArrival">Приходная накладная</el-dropdown-item>
                    <el-dropdown-item @click="onRefund">Возврат поставщику</el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
        <el-dropdown class="ml-3">
            <el-button type="success" plain>
                Связанные документы<el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item>Сделать дерево всех документов</el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
        <el-button type="danger" class="ml-5" @click="onWork">Отмена проведения</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.supply.add-product', {supply: supply.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.supply.add-products', {supply: supply.id})" class="ml-3"/>
        <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
    </template>
    <span class="ml-auto">
        Сумма <el-tag type="danger" size="large">{{ func.price(supply.amount, supply.currency) }}</el-tag>
    </span>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'

const props = defineProps({
    supply: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.supply.completed', {supply: props.supply.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.supply.work', {supply: props.supply.id}), {
        method: "post",
    })
}
function onPayment() {
    router.visit(route('admin.accounting.supply.payment', {supply: props.supply.id}), {
        method: "post",
    })
}
function onArrival() {
    router.visit(route('admin.accounting.supply.arrival', {supply: props.supply.id}), {
        method: "post",
    })
}
function onRefund() {
    router.visit(route('admin.accounting.supply.refund', {supply: props.supply.id}), {
        method: "post",
    })
}


</script>
