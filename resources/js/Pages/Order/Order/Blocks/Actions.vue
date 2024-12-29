<template>

    <template v-if="order.status.is_new || order.status.is_manager">
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
                      :formatter="val => func.MaskCount(val, 0, 100)"
                      clearable
                      class="ml-1" style="width: 80px"
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
    <template v-if="order.status.is_awaiting">

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
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
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
/*
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
*/
</script>
