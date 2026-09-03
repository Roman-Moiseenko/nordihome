<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl flex">

            <div style="width: 100%">
            Заказ <span v-if="order.number" class="ml-1 mr-1"> № {{ order.number }}</span> покупателя [{{ order.status }}]

            <span class="ml-2 mr-2">от</span>
            </div>
            <EditField v-if="is_new || is_awaiting" :field="order.created_at" @update:field="setCreated" :isdate="true" />
            <span v-else>
                {{ func.date(order.created_at)}}
            </span>

            <el-tooltip content="История заказа" placement="right-start" effect="dark">
                <el-button size="small" type="primary" class="ml-2" plain @click="handleLogOrder">
                    <i class="fa-light fa-rectangle-history"></i>
                </el-button>
            </el-tooltip>
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <OrderInfo :order="order" :storages="storages" :mainStorage="mainStorage" :traders="traders" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <OrderActions :order="order" :additions="additions" :storages="storages" />
            </div>
        </el-affix>

        <div v-if="is_new || is_awaiting">
            <div v-if="order.inStock.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-cyan-800">Товары в наличии</h2>
                <OrderItemsNew :items=[...order.inStock] :order-id="order.id"/>
            </div>
            <div v-if="order.preOrder.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-orange-800">Товары под заказ</h2>
                <OrderItemsNew :items=[...order.preOrder] :order-id="order.id"/>
            </div>
        </div>
        <div v-if="is_issued">
            <div v-if="order.inStock.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-cyan-800">Товары на выдачу</h2>
                <OrderItemsIssued :items=[...order.inStock] />
            </div>
            <div v-if="order.preOrder.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
                <h2 class="font-medium text-orange-800">Товары под заказ</h2>
                <OrderItemsIssued :items=[...order.preOrder] />
            </div>
        </div>
        <div v-if="is_view">
            <OrderItemsView :items=[...order.items] />
        </div>
        <div v-if="order.additions.length > 0" class="mt-1 py-1 bg-white rounded-md">
            <h2 class="font-medium text-green-800">Услуги</h2>
            <OrderAdditions v-if="is_new || is_awaiting" :additions=[...order.additions] :order-id="order.id" />
            <OrderAdditionsIssued v-if="is_issued" :additions=[...order.additions]  :order-id="order.id"/>
            <OrderAdditionsView v-if="is_view" :additions=[...order.additions] :order-id="order.id" />

        </div>

    </el-config-provider>
</template>
<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {computed, defineProps, provide} from "vue";
import OrderActions from "./Blocks/Actions.vue";
import OrderInfo from "./Blocks/Info.vue";
import Active from "@Comp/Elements/Active.vue";
import OrderItemsNew from "./Blocks/ItemsNew.vue"
import OrderItemsIssued from "./Blocks/ItemsIssued.vue"
import OrderItemsView from "./Blocks/ItemsView.vue"
import OrderAdditions from  "./Blocks/Additions.vue"
import OrderAdditionsIssued from  "./Blocks/AdditionsIssued.vue"
import OrderAdditionsView from  "./Blocks/AdditionsView.vue"
import EditField from "@Comp/Elements/EditField.vue";
import {func} from '@Res/func.js'
import axios from "axios";
import { ElMessage, ElMessageBox } from 'element-plus'

const props = defineProps({
    order: Object,
    title: {
        type: String,
        default: 'Заказ покупателя',
    },

    additions: Array,
    storages: Array,
    staffs: Array,
    mainStorage: Object,
    traders: Array,
    order_related: Array,
})

const open = () => {
    ElMessageBox.confirm(
        'Взять заказ в работу?',
        'Менеджер не назначен',
        {
            confirmButtonText: 'Взять',
            cancelButtonText: 'Отмена',
            type: 'warning',
        }
    )
        .then(() => {
            router.visit(route('admin.order.take', {id: props.order.id}), {
                method: "post",
                data: {},
                preserveScroll: true,
                preserveState: false,
            })

        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Менеджер не назначен',
            })
        })
}

if (props.order.staffId === null) {
    open()
}

provide('$order_related', props.order_related)
const is_new = computed(() => {
    return props.order.status === "new" || props.order.status === "draft"
})
const is_awaiting = computed(() => {
    return props.order.status === "awaiting"
})
const is_issued = computed(() => {
    return props.order.status === "prepaid" || props.order.status === "paid"
})
const is_view = computed(() => {
    return !is_new.value && !is_issued.value && !is_awaiting.value
})
provide("$status", {
    is_new,
    is_awaiting,
    is_issued,
    is_view,
})

function handleLogOrder() {
    router.get(route('admin.order.log', {order: props.order.id}))
}
function setCreated(val) {
    axios.post(route('admin.order.set-info', {id: props.order.id}), {createdAt: func.datetime(val),}).then(result => {
        props.order.created_at = result.data;
    })
}
</script>
<style scoped>

</style>
