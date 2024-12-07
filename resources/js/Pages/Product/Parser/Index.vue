<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Спарсенные Товары</h1>
        <div class="flex mt-5">

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.product" placeholder="Товар" class="mt-1"/>
                <el-select filterable v-model="filter.category" placeholder="Категория" class="mt-1">
                    <el-option v-for="item in categories" :key="item.id" :label="item.name"
                               :value="item.id"/>
                </el-select>

            </TableFilter>
        </div>
        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.product.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="product.code" label="Артикул" width="160"/>
                <el-table-column label="Товар">
                    <template #default="scope">
                        <div>{{ scope.row.product.name }}</div>
                        <div class="show-on-hover items-center">
                            <el-link
                                @click="onFragile(scope.row.id)"
                                :type="scope.row.fragile ? 'success' : 'warning'"
                                :underline="false"
                            >
                                {{ scope.row.fragile ? 'Не хрупкий' : 'Хрупкий' }}
                            </el-link>
                            &nbsp;|&nbsp;
                            <el-link
                                @click="onSanctioned(scope.row.id)"
                                :type="scope.row.sanctioned ? 'success' : 'warning'"
                                :underline="false"
                            >
                                {{ scope.row.sanctioned ? 'Не санкционный' : 'Санкционный' }}
                            </el-link>
                            &nbsp;|&nbsp;
                            <Link type="primary" :href="route('admin.product.edit', {product: scope.row.product_id})">К товару</Link>
                            &nbsp;|&nbsp;
                            <el-link
                                @click="onBlock(scope.row.id)"
                                :type="scope.row.order ? 'danger' : 'success'"
                                :underline="false"
                            >
                                {{ scope.row.order ? 'Заблокировать' : 'Разблокировать' }}
                            </el-link>

                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="product.category" label="Основная категория" width="240" show-overflow-tooltip />
                <el-table-column prop="packs" label="Пачек" width="100" align="center"/>
                <el-table-column prop="price" label="Цена (Zl)" width="120" align="center"/>
                <el-table-column prop="fragile" label="Хруп." width="60" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.fragile" />
                    </template>
                </el-table-column>
                <el-table-column prop="sanctioned" label="Санкц." width="70" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.sanctioned" />
                    </template>
                </el-table-column>
                <el-table-column prop="order" label="Дост." width="60" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.order" />
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="parsers.current_page"
            :per_page="parsers.per_page"
            :total="parsers.total"
        />
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import {Head, Link, router} from "@inertiajs/vue3";
import {useStore} from "@Res/store.js"
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {reactive, ref} from "vue";


const store = useStore();
const props = defineProps({
    parsers: Object,
    title: {
        type: String,
        default: 'Спарсенные Товары',
    },
    filters: Array,
    categories: Array,
})
const tableData = ref([...props.parsers.data])
const filter = reactive({
    product: props.filters.product,
    category: props.filters.category,
})

function onFragile(id){
    router.visit(route('admin.product.parser.fragile', {parser: id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onSanctioned(id){
    router.visit(route('admin.product.parser.sanctioned', {parser: id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onBlock(id){
    router.visit(route('admin.product.parser.block', {parser: id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
</script>

<style lang="scss">
.show-on-hover {
    display: none;
}
 .el-table__row:hover {
     .show-on-hover {
         display: block;
     }
 }
</style>
