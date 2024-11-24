<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Товары в корзине</h1>
        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="code" label="Артикул" width="180"/>
                <el-table-column prop="name" label="Товар"  show-overflow-tooltip />
                <el-table-column prop="quantity" label="Кол-во в корзине"/>
            </el-table>
        </div>
        <pagination
            :current_page="products.current_page"
            :per_page="products.per_page"
            :total="products.total"
        />
    </el-config-provider>
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'

const props = defineProps({
    products: Object,
    title: {
        type: String,
        default: 'Товары в корзине',
    },
})
const tableData = ref([...props.products.data])


</script>

