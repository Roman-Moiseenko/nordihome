<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Бренд товаров {{ brand.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <BrandInfo :brand="brand" />
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
            </el-table>
            <pagination
                :current_page="brand.products.current_page"
                :per_page="brand.products.per_page"
                :total="brand.products.total"
            />
        </div>
    </el-config-provider>

</template>
<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import ru from 'element-plus/dist/locale/ru.mjs'
import BrandInfo from  './Block/Info.vue'
import Pagination from "@Comp/Pagination.vue";

const props = defineProps({
    brand: Object,
    title: {
        type: String,
        default: 'Карточка бренда товаров',
    },

})
const store = useStore();
const tableData = ref([...props.brand.products.data])

function routeClick(row) {
    router.get(route('admin.product.show', {product: row.id}))
}
</script>

