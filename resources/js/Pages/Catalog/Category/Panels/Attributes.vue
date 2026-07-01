<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-pallet-boxes"></i>
                <span> Атрибуты</span>
            </span>
        </template>

        <div v-loading="loading">
        <h2 class="font-medium mt-3 mb-1">Родительские атрибуты</h2>

        <el-table
                v-if="parentAttributes.length > 0"
                :data="parentAttributes"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="image" label="Иконка" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="name" label="Атрибут" width="220" show-overflow-tooltip/>
            <el-table-column prop="group" label="Группа" width="220" show-overflow-tooltip/>
            <el-table-column prop="filter" label="Фильтр" width="160" align="center">
                <template #default="scope">
                    <Active :active="scope.row.filter"/>
                </template>
            </el-table-column>
            <el-table-column prop="type_text" label="Тип" show-overflow-tooltip/>
        </el-table>
            <p v-else class="text-gray-400 italic mt-2">Нет родительских атрибутов</p>

        <h2 class="font-medium mt-3 mb-1">Собственные атрибуты</h2>
        <el-table
                v-if="selfAttributes.length > 0"
                :data="selfAttributes"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="image" label="Иконка" width="80">
                <template #default="scope">
                    <img v-if="scope.row.image" :src="scope.row.image" style="width: 40px; height: 40px; ">
                </template>
            </el-table-column>
            <el-table-column prop="name" label="Атрибут" width="220" show-overflow-tooltip/>
            <el-table-column prop="group" label="Группа" width="220" show-overflow-tooltip/>
            <el-table-column prop="filter" label="Фильтр" width="160" align="center">
                <template #default="scope">
                    <Active :active="scope.row.filter"/>
                </template>
            </el-table-column>
            <el-table-column prop="type_text" label="Тип" show-overflow-tooltip/>
        </el-table>
            <p v-else class="text-gray-400 italic mt-2">Нет собственных атрибутов</p>
        </div>

    </el-tab-pane>
</template>

<script setup lang="ts">
import {defineProps, onMounted, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import axios from 'axios';

const props = defineProps({
    categoryId: Number,
})

interface Attribute {
    id: number
    name: string
    group: string
    filter: boolean
    type_text: string
    image: string | null
}

const loading = ref(false)
const parentAttributes = ref<Attribute[]>([])
const selfAttributes = ref<Attribute[]>([])

onMounted(() => {
    fetchAttributes()
})

function fetchAttributes() {
    loading.value = true
    axios.get(route('admin.catalog.category.attributes', {id: props.categoryId}))
        .then(response => {
            parentAttributes.value = response.data.parent ?? []
            selfAttributes.value = response.data.self ?? []
        })
        .finally(() => {
            loading.value = false
        })
}

function routeClick(row) {
    router.get(route('admin.catalog.attribute.show', {attribute: row.id}))
}
</script>
