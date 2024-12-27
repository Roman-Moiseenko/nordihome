<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Заказ покупателя
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <OrderInfo :order="order" :storages="storages" :mainStorage="mainStorage" />
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <OrderActions :order="order" :additions="additions" />
            </div>
        </el-affix>

        <div v-if="order.in_stock.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
            <h2 class="font-medium text-cyan-800">Товары в наличии</h2>
            <OrderItems :items=[...order.in_stock] :status="order.status"/>
        </div>
        <div v-if="order.pre_order.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
            <h2 class="font-medium text-orange-800">Товары под заказ</h2>
            <OrderItems :items=[...order.pre_order] :status="order.status" />
        </div>
        <div v-if="order.additions.length > 0" class="mt-1 px-3 py-1 bg-white rounded-md">
            <h2 class="font-medium text-green-800">Услуги</h2>
            <OrderAdditions :additions=[...order.additions] :status="order.status" />
        </div>

    </el-config-provider>
</template>
<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
import {defineProps} from "vue";
import OrderActions from "./Blocks/Actions.vue";
import OrderInfo from "./Blocks/Info.vue";
import Active from "@Comp/Elements/Active.vue";
import OrderItems from "./Blocks/Items.vue"
import OrderAdditions from  "./Blocks/Additions.vue"

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
})
console.log(props.order)
</script>
<style scoped>

</style>
