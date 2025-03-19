<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Заказы поставщикам</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать документ
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-select v-model="create_id" placeholder="Поставщики" class="mt-1">
                    <el-option v-for="item in $props.distributors" :key="item.id" :label="item.name" :value="item.id"/>
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <el-badge :value="stack_count" class="item my-3" color="red" :hidden="stack_count == 0">
                <el-button type="success" class="p-4 ml-2" @click="stackButton">Стек заказов</el-button>
            </el-badge>
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
                <el-select v-model="filter.distributor" placeholder="Поставщики" class="mt-1">
                    <el-option v-for="item in distributors" :key="item.id" :label="item.name"
                               :value="item.id"/>
                </el-select>
                <el-select v-model="filter.staff_id" placeholder="Ответственный" class="mt-1">
                    <el-option v-for="item in staffs" :key="item.id" :label="func.fullName(item.fullname)"
                               :value="item.id"/>
                </el-select>
                <el-input v-model="filter.comment" placeholder="Комментарий" class="mt-1"/>
                <el-checkbox v-model="filter.draft" label="Не проведенные" :checked="filter.draft"/>
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
                <el-table-column label="ОПЛ" width="40" class-name="no-space-cell" align="center">
                    <template #default="scope">
                        <StatusGraph :value="scope.row.status_pay" />
                    </template>
                </el-table-column>
                <el-table-column label="ОТГ" width="40" class-name="no-space-cell" align="center">
                    <template #default="scope">
                        <StatusGraph :value="scope.row.status_out" />
                    </template>
                </el-table-column>
                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at)}}
                    </template>
                </el-table-column>
                <el-table-column prop="number" label="№" width="80" align="center"/>
                <el-table-column prop="distributor_name" label="Поставщик" width="260" show-overflow-tooltip/>
                <el-table-column prop="completed" label="Проведен" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.completed"/>
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Кол-во" width="100" align="center"/>
                <el-table-column prop="amount" label="Сумма" width="120">
                    <template #default="scope">
                        {{ func.price(scope.row.amount, scope.row.currency) }}
                    </template>
                </el-table-column>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                <!--el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/-->

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="warning"
                            @click.stop="handleCopy(scope.row)">
                            Copy
                        </el-button>
                        <AccountingSoftDelete
                            v-if="scope.row.trashed"
                            :restore="route('admin.accounting.supply.restore', {supply: scope.row.id})"
                            :small="true"
                            @destroy="onForceDelete(scope.row)"
                        />
                        <el-button
                            v-if="!scope.row.completed && !scope.row.trashed"
                            size="small"
                            type="danger" plain
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            For Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="supplies.current_page"
            :per_page="supplies.per_page"
            :total="supplies.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Заказ поставщику" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router, usePage} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import StatusGraph from "@Comp/Elements/StatusGraph.vue";
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import { IRowAccounting as IRow} from "@Res/interface"

const props = defineProps({
    supplies: Object,
    title: {
        type: String,
        default: 'Заказы поставщикам',
    },
    filters: Array,
    distributors: Array,
    stack_count: Number,
    staffs: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.supplies.data])
const filter = reactive({
    draft: props.filters.draft,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    distributor: props.filters.distributor,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
})
const create_id = ref<Number>(null)

const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.trashed === true) return 'danger-row'
    if (row.completed === 0) {
        return 'warning-row'
    }
    return ''
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.supply.destroy', {supply: row.id}), {soft: true});
}
function onForceDelete(row) {
    $delete_entity.show(route('admin.accounting.supply.full-destroy', {supply: row.id}));
}

function createButton() {
    router.get(route('admin.accounting.supply.create', {distributor: create_id.value}))
}
function stackButton() {
    router.get(route('admin.accounting.supply.stack'))
}
function routeClick(row) {
    router.get(route('admin.accounting.supply.show', {supply: row.id}))
}
function handleCopy(row) {
    router.post(route('admin.accounting.supply.copy', {supply: row.id}))
}
</script>

