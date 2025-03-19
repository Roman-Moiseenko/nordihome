<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Платежные поручения</h1>
        <div class="flex">
            <el-upload
                class="upload-demo"
                :action="route('admin.accounting.bank.upload')"
                :on-success="handleSuccess"
                :on-error="handleError"
            >
                <el-button type="primary">Загрузить из банка</el-button>
            </el-upload>
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
                <el-table-column prop="distributor_name" label="Поставщик" width="260" show-overflow-tooltip/>
                <el-table-column prop="completed" label="Проведен" width="120">
                    <template #default="scope">
                        <Active :active="scope.row.completed"/>
                    </template>
                </el-table-column>
                <el-table-column prop="amount" label="Сумма" width="100">
                    <template #default="scope">
                        {{ func.price(scope.row.amount, scope.row.currency) }}
                    </template>
                </el-table-column>
                <el-table-column prop="bank_purpose" label="Назначение" show-overflow-tooltip/>
                <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>

                <el-table-column label="Действия" align="right" width="200">
                    <template #default="scope">
                        <AccountingSoftDelete
                            v-if="scope.row.trashed"
                            :restore="route('admin.accounting.payment.restore', {payment: scope.row.id})"
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
            :current_page="payments.current_page"
            :per_page="payments.per_page"
            :total="payments.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="Платеж"/>
</template>
<script lang="ts" setup>
import {inject, reactive, ref} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import type {UploadProps, UploadUserFile} from 'element-plus'
import Active from '@Comp/Elements/Active.vue'
import AccountingSoftDelete from "@Comp/Accounting/SoftDelete.vue";
import { IRowAccounting as IRow} from "@Res/interface"

const props = defineProps({
    payments: Object,
    title: {
        type: String,
        default: 'Платежные поручения',
    },
    filters: Array,
    distributors: Array,
    staffs: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.payments.data])
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
    $delete_entity.show(route('admin.accounting.payment.destroy', {payment: row.id}), {soft: true});
}

function onForceDelete(row) {
    $delete_entity.show(route('admin.accounting.payment.full-destroy', {payment: row.id}));
}

function routeClick(row) {
    router.get(route('admin.accounting.payment.show', {payment: row.id}))
}

const handleSuccess: UploadProps['onSuccess'] = (response, uploadFile, uploadFiles) => {
    // console.log(response);
    router.get(route('admin.accounting.payment.index'));
}
const handleError: UploadProps['onError'] = (error, uploadFile, uploadFiles) => {
    console.log(error, uploadFile)
}
</script>
<style scoped>

</style>
