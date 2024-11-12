<template>
    <template v-if="refund.completed">
        <el-dropdown>
            <el-button type="primary">
                Создать на основании<el-icon class="el-icon--right"><arrow-down /></el-icon>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item>Есть ли на основании??</el-dropdown-item>
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
        <el-button type="danger" plain class="ml-auto" @click="onWork">Отмена</el-button>
    </template>
    <template v-else>
        <SearchAddProduct
            :route="route('admin.accounting.refund.add-product', {refund: refund.id})"
            :quantity="true"
        />
        <SearchAddProducts :route="route('admin.accounting.refund.add-products', {refund: refund.id})" class="ml-3"/>
        <el-button type="danger" class="ml-auto" @click="onCompleted">Провести</el-button>
    </template>
</template>

<script setup>
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    refund: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.refund.completed', {refund: props.refund.id}), {
        method: "post",
    })
}
function onWork() {
    router.visit(route('admin.accounting.refund.work', {refund: props.refund.id}), {
        method: "post",
    })
}

</script>
