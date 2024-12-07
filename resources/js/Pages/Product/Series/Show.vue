<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Серия {{ series.name }}</h1>
        <div class="flex mt-5">
            <SearchAddProduct
                :route="route('admin.product.series.add-product', {series: series.id})"
            />
            <SearchAddProducts :route="route('admin.product.series.add-products', {series: series.id})" class="ml-3"/>
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
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
    <DeleteEntityModal name_entity="Товар из серии" />
</template>
<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import ru from 'element-plus/dist/locale/ru.mjs'
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'

const props = defineProps({
    series: Object,
    title: {
        type: String,
        default: 'Карточка серии',
    },

})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.series.products])

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.series.del-product', {series: props.series.id, product_id: row.id}));
}

</script>

