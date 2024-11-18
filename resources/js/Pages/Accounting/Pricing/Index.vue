<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Установка цен</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="createButton">Создать документ</el-button>
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

                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="number" label="№ Документа" width="160"/>
                <el-table-column prop="completed" label="Проведен" width="120">
                    <template #default="scope">
                        <Active :active="scope.row.completed"/>
                    </template>
                </el-table-column>
                <el-table-column label="Основание">
                    <template #default="scope">
                        <span v-if="scope.row.arrival">
                            Приходная накладная № {{
                                scope.row.arrival.number
                            }} от {{ func.date(scope.row.arrival.created_at) }}
                        </span>
                    </template>
                </el-table-column>
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
        <pagination
            :current_page="pricings.current_page"
            :per_page="pricings.per_page"
            :total="pricings.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Установку цен"/>
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
    pricings: Object,
    title: {
        type: String,
        default: 'Ценообразование',
    },
    filters: Array,
    distributors: Array,
    staffs: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.pricings.data])
const filter = reactive({
    draft: props.filters.draft,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    distributor: props.filters.distributor,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
})
interface IRow {
    completed: number
}
const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.completed === 0) {
        return 'warning-row'
    }
    return ''
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.pricing.destroy', {pricing: row.id}));
}
function createButton() {
    router.post(route('admin.accounting.pricing.store'))
}
function routeClick(row) {
    router.get(route('admin.accounting.pricing.show', {pricing: row.id}))
}
function handleCopy(row) {
    router.post(route('admin.accounting.pricing.copy', {pricing: row.id}))
}
</script>
