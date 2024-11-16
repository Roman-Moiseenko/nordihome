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
                    <el-dropdown-item @click="onRefund(null)">Возврат поставщику</el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
        <AccountingOnBased :based="supply.based" />
        <AccountingPrint :print="supply.print" />
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

    <!--Возврат-->
    <el-dialog v-model="refundVisible" title="Shipping address" width="600">
        <template #header>
            <h2 class="font-medium text-center mb-3">Создать возврат на основе приходной накладной</h2>
            <div v-for="(arrival, index) in supply.arrivals" class="flex my-2">
                <span>
                    Приходная накладная № {{ arrival.number }}
                    <span v-if="arrival.incoming_number">({{ arrival.incoming_number }})</span>
                    от {{ func.date(arrival.created_at)}}
                    <span class="ml-2 text-green-800">{{ func.price(arrival.amount, arrival.currency) }}</span>
                </span>
                <el-button type="primary" class="ml-auto" @click="onRefund(arrival.id)">
                    <i class="fa-light fa-right"></i>
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Pages/AccountingOnBased.vue";
import AccountingPrint from "@Comp/Pages/AccountingPrint.vue";

const props = defineProps({
    supply: Object,
})
const refundVisible = ref(false)

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
function onRefund(id) {
    console.log(id);
    if (id === null) {
        refundVisible.value = true
    } else {
        router.visit(route('admin.accounting.arrival.refund', {arrival: id}), {
            method: "post",
        })
    }
}


</script>
