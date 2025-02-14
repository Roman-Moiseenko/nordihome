<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Группа аналогов {{ equivalent.name }} [{{ equivalent.category }}]</h1>

        <div class="flex mt-5">
            <SearchAddProduct
                :route="route('admin.product.equivalent.add-product', {equivalent: equivalent.id})"
                :search="route('admin.product.equivalent.search', {equivalent: equivalent.id})"
            />
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                v-loading="store.getLoading"
                @row-click="routeClick"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" width="300" show-overflow-tooltip/>
                <el-table-column prop="category" label="Основная категория" width="" show-overflow-tooltip />

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из группы" />
</template>
<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import ru from 'element-plus/dist/locale/ru.mjs'
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import {route} from "ziggy-js";

const props = defineProps({
    equivalent: Object,
    title: {
        type: String,
        default: 'Карточка группы аналогов',
    },

})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.equivalent.products])

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.equivalent.del-product', {equivalent: props.equivalent.id, product_id: row.id}));
}

function routeClick(row) {
    router.get(route('admin.product.edit', {product: row.id}))
}
</script>

