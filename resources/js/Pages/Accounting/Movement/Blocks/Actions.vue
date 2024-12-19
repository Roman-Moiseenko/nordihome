<template>
    <template v-if="movement.completed">
        <el-button v-if="movement.is_departure" type="warning" class="ml-5" @click="onDeparture">Товар убыл</el-button>
        <el-button v-if="movement.is_arrival" type="success" class="ml-5" @click="onArrival">Товар прибыл</el-button>

        <AccountingPrint />
        <AccountingWork v-if="movement.is_departure" :route="route('admin.accounting.movement.work', {movement: props.movement.id})" />
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.movement.add-product', {movement: movement.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.movement.add-products', {movement: movement.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.movement.completed', {movement: props.movement.id})" />
    </template>
    <AccountingOnBased :based="movement.based" :founded="movement.founded"/>
    <AccountingFilter />
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import {ElLoading} from "element-plus";
import AccountingFilter from "@Comp/Accounting/Filter.vue";

const props = defineProps({
    movement: Object,
    print: Array,
})

//+ 2 режима. Убыл, Прибыл
function onDeparture() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.movement.departure', {movement: props.movement.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onArrival() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.movement.arrival', {movement: props.movement.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}

</script>
