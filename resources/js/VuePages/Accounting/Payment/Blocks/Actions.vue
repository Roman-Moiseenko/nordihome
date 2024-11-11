<template>
    <template v-if="payment.completed">

        <el-button type="danger" @click="onWork">
            Отменить проведение
        </el-button>


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
        <el-button type="danger" class="ml-auto" @click="onCompleted">Провести</el-button>
    </template>
</template>

<script setup>
import {defineProps} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    payment: Object,
})

function onCompleted() {
    router.visit(route('admin.accounting.payment.completed', {payment: props.payment.id}), {
        method: "post",
    })
}

function onWork() {
    router.visit(route('admin.accounting.payment.work', {payment: props.payment.id}), {
        method: "post",
    })
}

</script>
