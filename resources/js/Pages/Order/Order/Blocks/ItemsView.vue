<template>
    <el-table
        :data="tableData"
        header-cell-class-name="nordihome-header"
        style="width: 100%;"
    >
        <el-table-column type="index" label="п/п"/>
        <el-table-column prop="product.code" label="Артикул" width="110"/>
        <el-table-column prop="product.name" label="Товар" width="320" show-overflow-tooltip />
        <el-table-column label="Базовая цена" width="230" align="center">
            <template #default="scope">
                {{ func.price(scope.row.base_cost) }}
            </template>
        </el-table-column>
        <el-table-column label="Продажа" width="230" align="center">
            <template #default="scope">
                <span class="text-green-800">{{ func.price(scope.row.sell_cost) }}</span>
                <span v-if="scope.row.percent > 0" class="text-green-800 ml-2"> -{{
                        scope.row.percent
                    }}%</span>
            </template>
        </el-table-column>
        <el-table-column label="Кол-во" width="110" align="center">
            <template #default="scope">

                <span>
                    <span class="text-green-800">{{ scope.row.quantity }}</span>
                </span>
            </template>
        </el-table-column>

        <el-table-column prop="comment" align="right" label="Комментарий" width="120" show-overflow-tooltip />
    </el-table>
</template>

<script setup lang="ts">
import {computed, inject, ref} from "vue"
import {func} from "@Res/func.js"
import Active from "@Comp/Elements/Active.vue";
import {router, Link} from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
})

const tableData = ref([...props.items])

</script>

<style lang="scss">
.el-input-group__append {
    padding: 0 8px;
}

.el-input[class*=bg-] {
    > .el-input__wrapper {
        background-color: unset;
    }
}
</style>
