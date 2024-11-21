<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">Хранилища (Магазины, Склады, Точки выдачи)</h1>
    <div class="flex">
        <el-popover :visible="visible_create" placement="bottom-start" :width="246">
            <template #reference>
                <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                    Добавить хранилище
                    <el-icon class="ml-1"><ArrowDown /></el-icon>
                </el-button>
            </template>
            <el-input v-model="storage_name" placeholder="Название" class="mt-1" />
            <div class="mt-2">
                <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
            </div>
        </el-popover>

    </div>
    <div class="mt-2 p-5 bg-white rounded-md">
        <el-table
            :data="[...props.storages.data]"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            @row-click="routeClick"
        >
            <el-table-column prop="name" label="Название"/>
            <el-table-column prop="address" label="Адрес" />
            <el-table-column label="Точка продажи">
                <template #default="scope">
                    <Active :active="scope.row.point_of_sale" />
                </template>
            </el-table-column>
            <el-table-column label="Точка выдачи">
                <template #default="scope">
                    <Active :active="scope.row.point_of_delivery" />
                </template>
            </el-table-column>
            <el-table-column prop="quantity" label="Товаров" />
        </el-table>
    </div>
</template>

<script setup>
import {Head, router} from "@inertiajs/vue3";
import {defineProps, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    storages: Object,
    title: {
        type: String,
        default: 'Хранилища',
    },
})
const visible_create = ref(false)
const storage_name = ref(null)

function routeClick(row) {
    router.get(route('admin.accounting.storage.show', {storage: row.id}))
}

function createButton() {
    router.post(route('admin.accounting.storage.store', {name: storage_name.value}))
}
</script>
<style scoped>

</style>
