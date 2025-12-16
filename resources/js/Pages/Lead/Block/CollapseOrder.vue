<template>
    <el-collapse :name="'order' + lead.id">
        <el-collapse-item name="1">
            <template #title>
                <el-tag effect="dark" size="large">Заказ #{{ lead.order.number }} на {{ func.price(lead.order.amount)}} </el-tag>

                <el-tag v-if="lead.assembly" effect="plain" type="warning" size="large">Сборка</el-tag>
                <el-tag v-if="lead.delivery" effect="plain" type="danger" size="large">Доставка</el-tag>
            </template>
            <Link :href="route('admin.order.show', {order: lead.order.id})" class="flex items-center w-full text-sm"
                  type="primary">Перейти к заказу <i class="fa-light fa-right ml-2"></i></Link>
            <el-tag type="warning" class="mt-2">Товары</el-tag>
            <div v-for="product in props.lead.order.products" class="flex">
                <el-tag type="warning" effect="plain">{{ product.code }}</el-tag>
                <span class="ml-auto">{{ product.quantity }} шт.</span>
            </div>
            <el-tag type="info" class="mt-2">Отгрузки</el-tag>
            <div v-for="expense in props.lead.order.expenses" class="flex">
                <el-tag type="info" effect="plain">{{ func.date(expense.created_at) }} </el-tag>
                <el-tag type="success" effect="plain" class="ml-auto">{{ expense.status }}</el-tag>
                <el-button type="primary" size="small" @click="router.get(route('admin.order.expense.show', {expense: expense.id}))"><i class="fa-light fa-right"></i></el-button>
            </div>
        </el-collapse-item>
    </el-collapse>
</template>

<script setup lang="ts">
import {func} from "@Res/func.js"
import {route} from "ziggy-js";
import {Link, router} from "@inertiajs/vue3";

const props = defineProps({
    lead: Object,
})
console.log(props.lead.order)
</script>
<style scoped>
.el-collapse {
    --el-collapse-header-height: 28px;
}
</style>
