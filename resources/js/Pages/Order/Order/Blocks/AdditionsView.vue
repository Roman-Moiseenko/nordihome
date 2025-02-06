<template>
    <el-table
        :data="tableDate"
        header-cell-class-name="nordihome-header"
        style="width: 100%;"
    >
        <el-table-column type="index" label="п/п"/>
        <el-table-column prop="name" label="Услуга" width="430"/>
        <el-table-column prop="" label="Сумма" width="240" align="center">
            <template #default="scope">
                <el-tag type="success" effect="dark">
                    {{ func.price(scope.row.amount * scope.row.quantity) }}
                </el-tag>
            </template>
        </el-table-column>
        <el-table-column prop="quantity" label="Кол-во" width="110" align="center">
            <template #default="scope">
                <div v-if="scope.row.is_quantity">
                    <el-tag type="primary" effect="dark">{{ scope.row.quantity }}</el-tag>
                </div>
                <div v-else>
                    -
                </div>
            </template>
        </el-table-column>
        <el-table-column label="Возврат" width="100" align="center">
            <template #default="scope">
                <span v-if="scope.row.refund">
                    <span class="text-red-600">{{ func.price(scope.row.refund) }}</span>
                </span>
            </template>
        </el-table-column>
        <el-table-column prop="comment" label="Комментарий" align="right" show-overflow-tooltip />

    </el-table>
</template>

<script setup lang="ts">
import {func} from "@Res/func.js"
import {defineProps, inject, ref} from "vue";
import {Link, router} from "@inertiajs/vue3";

const props = defineProps({
    additions: Array,
})
const tableDate = [...props.additions]

//const {is_new, is_issued, is_view} = inject('$status')

</script>

<style scoped>

</style>
