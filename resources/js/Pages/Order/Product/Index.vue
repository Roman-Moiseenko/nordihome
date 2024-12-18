<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Все товары</h1>

        <div class="flex">
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Товар"/>
                <el-select v-model="filter.category" placeholder="Выберите категорию" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                </el-select>
                <el-select v-model="filter.show" placeholder="Показать" class="mt-1">
                    <el-option key="active" value="active" label="Опубликованные"/>
                    <el-option key="not_sale" value="not_sale" label="Снятые с продажи"/>
                    <el-option key="draft" value="draft" label="Черновики"/>
                    <el-option key="delete" value="delete" label="Удаленные"/>
                </el-select>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="code" label="Артикул" width="120"/>
                <el-table-column prop="name" label="Название" show-overflow-tooltip>

                </el-table-column>
                <el-table-column prop="category_name" label="Категория" width="250" align="center"
                                 show-overflow-tooltip/>
                <el-table-column prop="published" label="Опубликован" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published"/>
                    </template>
                </el-table-column>
                <el-table-column prop="not_sale" label="В продаже" width="100" align="center">
                    <template #default="scope">
                        <Active :active="!scope.row.not_sale"/>
                    </template>
                </el-table-column>

            </el-table>
        </div>

        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />

    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, reactive, ref, defineProps} from "vue";
import {Head, Link, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import {route} from "ziggy-js";



const props = defineProps({
    products: Object,
    filters: Array,
    categories: Array,
    title: {
        type: String,
        default: 'Все товары',
    },
})
const store = useStore();
const tableData = ref([...props.products.data])
const filter = reactive({
    name: props.filters.name,
    category: props.filters.category,
    show: props.filters.show,
})
</script>
