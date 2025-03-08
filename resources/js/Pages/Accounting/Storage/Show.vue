<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Хранилище {{ storage.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <StorageInfo :storage="storage" />
        </div>
        <el-table :data="[...storage.items.data]"
                  header-cell-class-name="nordihome-header"
                  :row-class-name="tableRowClassName"
                  style="width: 100%;">
            <el-table-column prop="product.code" label="Артикул" width="160" />
            <el-table-column prop="product.name" label="Товар" show-overflow-toolti>
                <template #default="scope">
                    <Link type="info" :href="route('admin.product.edit', {product: scope.row.product.id})">{{ scope.row.product.name }}</Link>
                </template>
            </el-table-column>
            <el-table-column prop="cell" label="Ячейка" width="120" />
            <el-table-column prop="quantity" label="Фактическое" width="180" />
            <el-table-column prop="movement" label="Движение" width="180" />
            <el-table-column prop="reserve" label="В резерве" width="180" />
            <el-table-column prop="product.for_sell" label="Доступно" width="180" />
        </el-table>
        <pagination
            :current_page="storage.items.current_page"
            :per_page="storage.items.per_page"
            :total="storage.items.total"
        />

    </el-config-provider>
</template>

<script lang="ts" setup>
import {inject, ref, provide} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import StorageInfo from "./Blocks/Info.vue";

const props = defineProps({
    storage: Object,

    title: {
        type: String,
        default: 'Карточка Хранилища',
    },

})

interface IRow {
    quantity: number,
}
const tableRowClassName = ({row}: { row: IRow }) => {

    return ''
}
function classCostColor(row) {

    return ''
}

</script>
