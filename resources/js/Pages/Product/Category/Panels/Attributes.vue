<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-pallet-boxes"></i>
                <span> Атрибуты</span>
            </span>
        </template>
        <h2 class="font-medium mt-3 mb-1">Родительские атрибуты</h2>

        <el-table
            :data="[...category.attributes.parent]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="image" label="Иконка" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="name" label="Атрибут"  width="220" show-overflow-tooltip/>
            <el-table-column prop="group" label="Группа" width="220" show-overflow-tooltip/>
            <el-table-column prop="filter" label="Фильтр" width="160" align="center">
                <template #default="scope">
                    <Active :active="scope.row.filter" />
                </template>
            </el-table-column>
            <el-table-column prop="type_text" label="Тип" show-overflow-tooltip/>
        </el-table>

        <h2 class="font-medium mt-3 mb-1">Собственные атрибуты</h2>
        <el-table
            :data="[...category.attributes.self]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="image" label="Иконка" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="name" label="Атрибут"  width="220" show-overflow-tooltip/>
            <el-table-column prop="group" label="Группа" width="220" show-overflow-tooltip/>
            <el-table-column prop="filter" label="Фильтр" width="160" align="center">
                <template #default="scope">
                    <Active :active="scope.row.filter" />
                </template>
            </el-table-column>
            <el-table-column prop="type_text" label="Тип" show-overflow-tooltip/>
        </el-table>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    category: Object,
})
console.log(props.category.attributes)
function routeClick(row) {
    router.get(route('admin.product.attribute.show', {attribute: row.id}))
}
</script>
