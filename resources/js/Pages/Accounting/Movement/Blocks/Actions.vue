<template>
    <template v-if="movement.completed">
        <el-button v-if="movement.is_departure" type="warning" class="ml-5" @click="onDeparture">Товар убыл</el-button>
        <el-button v-if="movement.is_arrival" type="success" class="ml-5" @click="onArrival">Товар прибыл</el-button>

        <AccountingOnBased :based="movement.based" :founded="movement.founded"/>
        <AccountingPrint :print="print" />
        <el-button v-if="movement.is_departure" type="danger" class="ml-5" @click="onWork">Отменить проведение</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.movement.add-product', {movement: movement.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.movement.add-products', {movement: movement.id})" class="ml-3"/>
        <el-button type="danger" plain class="ml-5" @click="onCompleted">Провести документ</el-button>
    </template>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    movement: Object,
    print: Array,
})

function onCompleted() {
    router.visit(route('admin.accounting.movement.completed', {movement: props.movement.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.movement.work', {movement: props.movement.id}), {
        method: "post",
    })
}
//+ 2 режима . Убыл, Прибыл
function onDeparture() {
    router.visit(route('admin.accounting.movement.departure', {movement: props.movement.id}), {
        method: "post",
    })
}
function onArrival() {
    router.visit(route('admin.accounting.movement.arrival', {movement: props.movement.id}), {
        method: "post",
    })


}

</script>
