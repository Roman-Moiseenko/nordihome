<template>
    <template v-if="arrival.completed">
        <el-dropdown>
            <el-button type="primary">
                Создать на основании<el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item @click="onExpenses">Дополнительные расходы</el-dropdown-item>
                    <el-dropdown-item @click="onMovement">Перемещение запасов</el-dropdown-item>
                    <el-dropdown-item @click="onInvoice">Расходная накладная</el-dropdown-item>
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
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.arrival.add-product', {arrival: arrival.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.arrival.add-products', {arrival: arrival.id})" class="ml-3"/>
        <el-button type="danger" class="ml-auto" @click="onCompleted">Провести</el-button>
    </template>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    arrival: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.arrival.completed', {arrival: props.arrival.id}), {
        method: "post",
    })
}
function onExpenses() {
    router.visit(route('admin.accounting.arrival.expenses', {arrival: props.arrival.id}), {
        method: "post",
    })
}
function onMovement() {
    router.visit(route('admin.accounting.arrival.movement', {arrival: props.arrival.id}), {
        method: "post",
    })
}
function onInvoice() {
    router.visit(route('admin.accounting.arrival.invoice', {arrival: props.arrival.id}), {
        method: "post",
    })
}
function onRefund() {
    router.visit(route('admin.accounting.arrival.refund', {arrival: props.arrival.id}), {
        method: "post",
    })
}

</script>
