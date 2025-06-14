<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Заказы</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="handleCreate">Новый заказ</el-button>
            <!--div class="my-auto ml-5">
                <el-tag type="primary" size="large" effect="plain" class="mr-2">Показать: </el-tag>
                <el-checkbox v-model="filter.canceled" @click="onFilter('canceled')" :checked="filters.canceled">Отмененные</el-checkbox>
                <el-checkbox v-model="filter.completed" @click="onFilter('completed')" :checked="filters.completed">Исполненные</el-checkbox>
            </div-->
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-date-picker
                    v-model="filter.date_from"
                    type="date"
                    class="mt-1"
                    placeholder="Выберите дату с"
                    value-format="YYYY-MM-DD"
                />
                <el-date-picker
                    v-model="filter.date_to"
                    type="date"
                    class="mt-1"
                    placeholder="Выберите дату по"
                    value-format="YYYY-MM-DD"
                />
                <el-input v-model="filter.user" placeholder="Клиент" class="mt-1"/>
                <el-input v-model="filter.comment" placeholder="Комментарий" class="mt-1"/>
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
                :row-class-name="classes.TableCompleted"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column label="ОПЛ" width="40" class-name="no-space-cell" align="center">
                    <template #default="scope">
                        <StatusGraph :value="scope.row.status_pay" type="pay"/>
                    </template>
                </el-table-column>
                <el-table-column label="ОТГ" width="40" class-name="no-space-cell" align="center">
                    <template #default="scope">
                        <StatusGraph :value="scope.row.status_out" type="out"/>
                    </template>
                </el-table-column>
                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="number" label="Номер" width="160"/>
                <el-table-column prop="distributor_name" label="Клиент" width="260" show-overflow-tooltip>
                    <template #default="scope">
                        <div class="font-medium text-sm">{{ scope.row.user.name }}</div>
                        <div class="text-slate-700 text-xs">{{ func.phone(scope.row.user.phone) }}</div>
                    </template>
                </el-table-column>

                <el-table-column prop="amount" label="Сумма" width="120">
                    <template #default="scope">
                        {{ func.price(scope.row.amount) }}
                        <span v-if="scope.row.refund !== 0">
                            <el-tooltip content="Возврат" >
                                <el-tag type="danger" >{{ func.price(scope.row.refund)}}</el-tag>
                            </el-tooltip>
                        </span>
                    </template>
                </el-table-column>
                <el-table-column prop="status_text" label="Статус" show-overflow-tooltip/>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="warning"
                            @click.stop="handleCopy(scope.row)">
                            Copy
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="orders.current_page"
            :per_page="orders.per_page"
            :total="orders.total"
        />

    </el-config-provider>

</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps, provide} from "vue";
import {Head, router, usePage} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import StatusGraph from "@Comp/Elements/StatusGraph.vue";
import {classes} from "@Res/className"

const props = defineProps({
    orders: Object,
    title: {
        type: String,
        default: 'Заказы клиентов',
    },
    filters: Array,

    staffs: Array,
})
const store = useStore();
const tableData = ref([...props.orders.data])
const filter = reactive({
    condition: props.filters.condition,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    user: props.filters.user,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
    canceled: props.filters.canceled,
    completed: props.filters.completed,
})

function handleCreate() {
    router.post(route('admin.order.store'))
}
function routeClick(row) {
    router.get(route('admin.order.show', {order: row.id}))
}
function handleCopy(row) {
    router.post(route('admin.order.copy', {order: row.id}))
}
function onFilter(_check) {
    if (_check == 'canceled') filter.canceled = !filter.canceled
    if (_check == 'completed') filter.completed = !filter.completed
    router.get(usePage().url.split('?')[0], filter)
}

</script>
<style scoped>

</style>
