<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Списание товара</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create"
                               ref="buttonRef">
                        Создать документ
                        <el-icon class="ml-1">
                            <ArrowDown/>
                        </el-icon>
                    </el-button>
                </template>
                <el-select v-model="create_id" placeholder="Склад" class="mt-1">
                    <el-option v-for="item in storages" :key="item.id" :label="item.name" :value="item.id"/>
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button>
                    <el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>

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
                <el-select v-model="filter.storage" placeholder="Склад" class="mt-1">
                    <el-option v-for="item in storages" :key="item.id" :label="item.name"
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
                :row-class-name="classes.TableAccounting"
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
                <el-table-column prop="quantity" label="Кол-во" width="100"/>
                <el-table-column prop="amount" label="Сумма" width="120">
                    <template #default="scope">
                        {{ func.price(scope.row.amount, scope.row.currency) }}
                    </template>
                </el-table-column>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <AccountingSoftDelete
                            v-if="scope.row.trashed"
                            :restore="route('admin.accounting.departure.restore', {departure: scope.row.id})"
                            :small="true"
                            @destroy="onForceDelete(scope.row)"
                        />
                        <el-button
                            v-if="!scope.row.completed && !scope.row.trashed"
                            size="small"
                            type="danger"
                            plain
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            For Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="departures.current_page"
            :per_page="departures.per_page"
            :total="departures.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Заказ поставщику"/>
</template>
<script lang="ts" setup>
import {inject, reactive, ref} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'

import Active from '@Comp/Elements/Active.vue'
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import {classes} from "@Res/className"

const props = defineProps({
    departures: Object,
    title: {
        type: String,
        default: 'Расходные накладные',
    },
    filters: Array,
    storages: Array,
    staffs: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.departures.data])
const filter = reactive({
    draft: props.filters.draft,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    storage: props.filters.storage,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
})
const create_id = ref<Number>(null)

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.departure.destroy', {departure: row.id}), {soft: true});
}
function onForceDelete(row) {
    $delete_entity.show(route('admin.accounting.departure.full-destroy', {departure: row.id}));
}

function createButton() {
    router.post(route('admin.accounting.departure.store', {storage: create_id.value}))
}

function routeClick(row) {
    router.get(route('admin.accounting.departure.show', {departure: row.id}))
}
</script>
<style scoped>

</style>
