<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        {{ func.fullName(user.fullname) }}
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <UserInfo :user="user" :organizations="organizations" :deliveries="deliveries" :type_pricing="type_pricing"/>
    </div>

    <el-config-provider :locale="ru">
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="[...user.orders.data]"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                :row-class-name="tableRowClassName"
                v-loading="store.getLoading"
            >
                <el-table-column type="expand">
                    <template #default="scope">
                        <h3 class="font-medium">Товары</h3>
                        <el-table :data="scope.row.items" :border="true">
                            <el-table-column prop="product.code" label="Артикул" width="120"/>
                            <el-table-column prop="product.name" label="Название" show-overflow-tooltip/>
                            <el-table-column prop="preorder" label="В наличии" align="center" width="100">
                                <template #default="scope">
                                    <Active :active="!scope.row.preorder" />
                                </template>
                            </el-table-column>
                            <el-table-column prop="quantity" label="Кол-во" align="center" width="160"/>
                            <el-table-column prop="sell_cost" label="Цена" align="center" width="160">
                                <template #default="scope">
                                    {{ func.price(scope.row.sell_cost) }}
                                </template>
                            </el-table-column>
                            <el-table-column prop="amount" label="Сумма">
                                <template #default="scope">
                                    {{ func.price(scope.row.quantity * scope.row.sell_cost) }}
                                </template>
                            </el-table-column>
                        </el-table>
                        <h3 class="mt-2 font-medium">Услуги</h3>
                        <el-table :data="scope.row.additions" :border="true">
                            <el-table-column prop="purposeText" label="Услуга" width="260"/>
                            <el-table-column prop="amount" label="Сумма" width="160" align="center">
                                <template #default="scope">
                                    {{ func.price(scope.row.amount) }}
                                </template>
                            </el-table-column>
                            <el-table-column prop="comment" label="Комментарий" align="center"/>
                        </el-table>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="Дата">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Товаров" width="100" align="center" />
                <el-table-column label="Сумма по заказу" align="center">
                    <template #default="scope">
                        {{ func.price(scope.row.amount) }}
                    </template>
                </el-table-column>
                <el-table-column label="Оплачено" align="center">
                    <template #default="scope">
                        {{ func.price(scope.row.payment) }}
                    </template>
                </el-table-column>
                <el-table-column prop="status" label="Статус" width="260" align="center"/>
                <el-table-column label="" width="160" align="right">
                    <template #default="scope">
                        <Link type="primary" :href="route('admin.order.show', {order: scope.row.id})">К заказу</Link>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="user.orders.current_page"
            :per_page="user.orders.per_page"
            :total="user.orders.total"
        />
    </el-config-provider>
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router, Link} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import UserInfo from './Blocks/Info.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    user: Object,
    title: {
        type: String,
        default: 'Карточка клиента',
    },
    filters: Array,
    organizations: Object,
    deliveries: Array,
    type_pricing: Array,
})
const store = useStore();
interface IRow {
    amount: number,
    payment: number,
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.amount > row.payment) return 'warning-row'
    return ''
}
</script>
