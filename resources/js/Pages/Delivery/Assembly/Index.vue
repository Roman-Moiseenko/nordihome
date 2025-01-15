<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Заказы на сборку</h1>
        <div class="flex">
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
                    <el-option v-for="item in works" :key="item.id" :label="func.fullName(item.fullname)"
                               :value="item.id"/>

                </el-select>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="number" label="Номер" width="100"/>
                <el-table-column prop="recipient" label="Клиент" width="260" show-overflow-tooltip>
                    <template #default="scope">
                        <div class="font-medium text-sm">{{ func.fullName(scope.row.recipient) }}</div>
                        <div class="text-slate-700 text-xs">{{ func.phone(scope.row.phone) }}</div>
                    </template>
                </el-table-column>

                <el-table-column prop="type_text" label="Доставка" width="180">

                </el-table-column>
                <el-table-column prop="status_text" label="Статус">
                    <template #default="scope">
                        <el-tag :type="statusType(scope.row.status)">{{ scope.row.status_text }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="work" label="Ответственный" >
                    <template #default="scope">
                        <span v-if="scope.row.work">{{ func.fullName(scope.row.work.fullname) }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-popover v-if="scope.row.status.is_assembly"

                                    :visible="visible_assembly"
                                    placement="bottom-start" :width="246">
                            <template #reference>
                                <el-button type="primary" class="p-4 mr-2" @click.stop="visible_assembly = !visible_assembly">
                                    Назначить
                                    <el-icon class="ml-1"><ArrowDown /></el-icon>
                                </el-button>
                            </template>
                            <el-select v-model="worker_id">
                                <el-option v-for="item in works" :value="item.id" :label="func.fullName(item.fullname)" />
                            </el-select>
                            <div class="mt-2">
                                <el-button @click="visible_assembly = false">Отмена</el-button>
                                <el-button @click="onAssembly(scope.row)" type="primary">Назначить</el-button>
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="expenses.current_page"
            :per_page="expenses.per_page"
            :total="expenses.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Заказ поставщику"/>
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
import StatusGraph from "@Comp/Elements/StatusGraph.vue";

const props = defineProps({
    expenses: Object,
    title: {
        type: String,
        default: 'Заказы на сборку',
    },
    filters: Array,

    works: Array,
})
console.log(props.expenses)

const store = useStore();

const tableData = ref([...props.expenses.data])
const filter = reactive({
    condition: props.filters.condition,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    user: props.filters.user,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
})
const worker_id = ref(null)
const visible_assembly = ref(false)
interface IRow {
    completed: number
}

const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.completed === 0) {
        return 'warning-row'
    }
    return ''
}


function routeClick(row) {
    router.get(route('admin.order.show', {order: row.id}))
}

function onAssembly(row) {
    router.visit(route('admin.delivery.assembling', {expense: row.id}), {
        method: "post",
        data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            visible_assembly.value = false
        }
    })
}

function statusType(status) {
    if (status.is_assembly) return 'danger'
    if (status.is_assembling) return 'warning'
    if (status.is_delivery) return 'primary'
    if (status.is_completed) return 'success'
    return 'info'
}

</script>
<style scoped>

</style>
