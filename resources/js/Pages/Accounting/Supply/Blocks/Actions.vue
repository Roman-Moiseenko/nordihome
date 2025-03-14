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
        <AccountingWork :route="route('admin.accounting.supply.work', {supply: props.supply.id})" />
    </template>
    <template v-else-if="!supply.trashed">
        <SearchAddProduct
            :route="route('admin.accounting.supply.add-product', {supply: supply.id})"
            :quantity="true"
            :create="true"
        />
        <SearchAddProducts :route="route('admin.accounting.supply.add-products', {supply: supply.id})" class="ml-3"/>
        <AccountingCompleted :route="route('admin.accounting.supply.completed', {supply: supply.id})" />
    </template>
    <template v-else>
        <AccountingSoftDelete
            :restore="route('admin.accounting.supply.restore', {supply: supply.id})"
            @destroy="onForceDelete"
        />
    </template>
    <AccountingOnBased />
    <AccountingPrint />
    <AccountingFilter />
    <span class="ml-auto">
        Позиций <el-tag type="primary" effect="dark" size="large">{{ supply.positions }}</el-tag>
        Товаров <el-tag type="warning" effect="dark" size="large">{{ supply.quantity }} шт</el-tag>
        Сумма <el-tag type="danger" effect="dark" size="large">{{ func.price(supply.amount, supply.currency) }}</el-tag>
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

    <DeleteEntityModal name_entity="Заказ поставщику" name="document"/>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps, inject, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import AccountingOnBased from "@Comp/Accounting/OnBased.vue";
import AccountingPrint from "@Comp/Accounting/Print.vue";
import { ElLoading } from 'element-plus'
import AccountingCompleted from "@Comp/Accounting/Completed.vue";
import AccountingWork from "@Comp/Accounting/Work.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import AccountingFilter from "@Comp/Accounting/Filter.vue"

const props = defineProps({
    supply: Object,
    print: Array,
})
const refundVisible = ref(false)
const $delete_entity = inject("$delete_entity")
function onForceDelete() {
    $delete_entity.show(route('admin.accounting.supply.full-destroy', {supply: props.supply.id}), {name: "document"});
}
function onPayment() {
    router.visit(route('admin.accounting.supply.payment', {supply: props.supply.id}), {
        method: "post",
    })
}
function onArrival() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.accounting.supply.arrival', {supply: props.supply.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onRefund(id) {
    if (id === null) {
        refundVisible.value = true
    } else {
        const loading = ElLoading.service({
            lock: false,
            text: 'Создание документа',
            background: 'rgba(0, 0, 0, 0.7)',
        })
        router.visit(route('admin.accounting.arrival.refund', {arrival: id}), {
            method: "post",
            onSuccess: page => {
                loading.close()
            }
        })
    }
}
</script>
