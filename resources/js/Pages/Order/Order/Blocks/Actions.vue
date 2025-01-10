<template>

    <template v-if="is_new">
        <SearchAddProduct
            :route="route('admin.order.add-product', {order: order.id})"
            :quantity="true"
            :preorder="true"
            :create="true"
        />
        <SearchAddProducts :route="route('admin.order.add-products', {order: order.id})" class="ml-3"/>
        <SelectAddition :additions="additions" :order="order"/>
        <div class="flex ml-1">
            <span class="ml-2 my-auto text-red-800">Скидка: </span>

            <el-input v-model="form.manual"
                      clearable
                      :formatter="val => func.MaskCount(val, 0)"
                      @change="setDiscount('manual')"
                      :disabled="iSaving"
                      style="width: 110px">
                <template #append>₽</template>
            </el-input>
            <el-input v-model="form.percent"
                      :formatter="val => func.MaskFloat(val)"
                      clearable
                      class="ml-1" style="width: 90px"
                      @change="setDiscount('percent')"
                      :disabled="iSaving"
            >
                <template #append>%</template>
            </el-input>
            <el-input v-model="form.coupon" clearable class="ml-2" style="width: 80px" placeholder="Купон"
                      @change="setDiscount('coupon')"
                      :disabled="iSaving"/>

        </div>

    </template>

    <el-dropdown v-if="is_awaiting || is_issued">
        <el-button type="primary mr-2">
            Создать на основании<el-icon class="el-icon--right"><arrow-down /></el-icon>
        </el-button>
        <template #dropdown>
            <el-dropdown-menu>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('cash')">Оплата в кассу</el-dropdown-item>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('card')">Оплата по карту</el-dropdown-item>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onPayment('account')">Оплата по счету</el-dropdown-item>
                <el-dropdown-item v-if="!order.status.is_paid" @click="onFindPayment">Найти оплату</el-dropdown-item>

                <el-dropdown-item v-if="is_issued" @click="onMovement">Выдать из магазина</el-dropdown-item>
                <el-dropdown-item v-if="is_issued" @click="onMovement">Выдать со склада</el-dropdown-item>
                <el-dropdown-item v-if="is_issued" @click="onMovement">Распоряжение на отгрузку</el-dropdown-item>

            </el-dropdown-menu>
        </template>
    </el-dropdown>

    <template v-if="!is_new && !is_awaiting">
        <el-dropdown v-if="is_awaiting || is_issued">
            <el-button type="success" class="mr-2">
                Платежи <el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <div v-for="item in order.payments" class="p-2">
                    <Link type="primary" :href="route('admin.order.payment.show', {payment: item.id})">Платеж на сумму {{ func.price(item.amount) }} [{{ item.method_text }}]</Link>
                </div>
            </template>
        </el-dropdown>
        <el-dropdown v-if="order.movements.length > 0">
            <el-button type="warning" class="mr-2">
                Перемещения <el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <div v-for="item in order.movements" class="p-2">
                    <Link type="warning"
                          :href="route('admin.accounting.movement.show', {movement: item.id})">Перемещение №{{ item.number }} [{{ item.status_text }}]</Link>
                </div>
            </template>
        </el-dropdown>

    </template>
    <template v-if="order.status.is_prepaid">

    </template>
    <template v-if="order.status.is_paid">

    </template>
    <template v-if="order.status.is_completed">

    </template>
    <template v-if="order.status.is_canceled">

    </template>

</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps, inject, reactive, ref} from "vue";
import {Link, router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import {ElLoading} from "element-plus";
import SelectAddition from "@Page/Order/Order/Blocks/SelectAddition.vue";


const props = defineProps({
    order: Object,
    additions: Array,
})
const iSaving = ref(false)
const form = reactive({
    coupon: null,
    manual: props.order.amount.manual,
    percent: props.order.amount.percent,
    action: null,
})
const {is_new, is_awaiting, is_issued, is_view} = inject('$status')
function setDiscount(action) {
    form.action = action
    iSaving.value = true
    router.visit(route('admin.order.set-discount', {order: props.order.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}
function onPayment(val) {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.payment.create', {order: props.order.id}), {
        method: "post",
        data: {method: val},
        onSuccess: page => {
            loading.close()
        }
    })
    //router.post(route('admin.order.payment.create', {order: props.order.id}), {})

}
function onExpense(val) {
    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(route('admin.order.expense.create', {order: props.order.id}), {
        method: "post",
        onSuccess: page => {
            loading.close()
        }
    })
}
function onFindPayment() {
    //TODO Модальное Окно с Загрузкой всех платежек по данному покупателю
    // props.order.shopper_id
}
/*
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
*/
</script>
