<template>
    <Layout>
        <Head><title>{{ title }}</title></Head>
        <el-config-provider :locale="ru">
            <h1 class="font-medium text-xl">Стек заказов</h1>
            <div class="flex">
                <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                    <el-select v-model="filter.brand" placeholder="Бренд" class="mt-1">
                        <el-option v-for="item in brands" :key="item.id" :label="item.name"
                                   :value="item.id"/>
                    </el-select>
                    <el-select v-model="filter.staff_id" placeholder="Ответственный" class="mt-1">
                        <el-option v-for="item in staffs" :key="item.id" :label="func.fullName(item.fullname)"
                                   :value="item.id"/>
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
                    <el-table-column label="Дата" width="120">
                        <template #default="scope">
                            {{ func.date(scope.row.created_at)}}
                        </template>
                    </el-table-column>
                    <el-table-column prop="code" label="Артикул" width="160"/>
                    <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
                    <el-table-column prop="quantity" label="Кол-во" width="100"/>
                    <el-table-column prop="founded" label="Основание" width="260">
                        <template #default="scope">
                            <el-link v-if="scope.row.order_id" :href="route('admin.order.show', {order: scope.row.order_id})">{{ scope.row.founded }}</el-link>
                            <span v-else>{{ scope.row.founded }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="brand" label="Производитель" width="260"/>
                    <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>
                    <!-- Повторить -->
                    <el-table-column label="Действия" align="right">
                        <template #default="scope">
                            <el-button v-if="!scope.row.order_id"
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
            <pagination
                :current_page="stacks.current_page"
                :per_page="stacks.per_page"
                :total="stacks.total"
            />
        </el-config-provider>
        <DeleteEntityModal name_entity="Товар из стека" />
    </Layout>
</template>
<script setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router, Link} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Layout from "@Comp/Layout.vue";
import Active from '@Comp/Elements/Active.vue'

const props = defineProps({
    stacks: Object,
    title: {
        type: String,
        default: 'Стек заказов',
    },
    brands: Array,
    filters: Array,
    staffs: Array,

})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.stacks.data])
const filter = reactive({
    founded: props.filters.founded,
    staff_id: props.filters.staff_id,
    brand: props.filters.brand,

})

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.supply.del-stack', {stack: row.id}));
}
</script>
