<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Платежи клиентов</h1>
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
                <el-input v-model="filter.user" placeholder="Клиент" class="mt-1"/>
                <el-input v-model="filter.order" placeholder="№ заказа" class="mt-1"/>
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
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >

                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="user_name" label="Клиент" width="260" show-overflow-tooltip>
                    <template #default="scope">
                        <div class="font-medium text-sm">{{ scope.row.user_name }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="Заказ">
                    <template #default="scope">
                        № {{ scope.row.order.number }} от {{ func.date(scope.row.order.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="amount" label="Сумма" width="120">
                    <template #default="scope">
                        {{ func.price(scope.row.amount) }}
                    </template>
                </el-table-column>
                <el-table-column prop="method_text" label="Способ оплаты" />
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
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
            :current_page="payments.current_page"
            :per_page="payments.per_page"
            :total="payments.total"
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
import type {UploadProps} from "element-plus";

const props = defineProps({
    payments: Object,
    title: {
        type: String,
        default: 'Платежи клиентов',
    },
    filters: Array,

    staffs: Array,
})
console.log(props.payments)

const store = useStore();

const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.payments.data])
const filter = reactive({
    condition: props.filters.condition,
    staff_id: props.filters.staff_id,
    comment: props.filters.comment,
    user: props.filters.user,
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
})
const create_id = ref<Number>(null)

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
    //$delete_entity.show(route('admin.order.destroy', {order: row.id}));
}

function handleCreate() {
    router.post(route('admin.order.store'))
}

function routeClick(row) {
    router.get(route('admin.order.show', {order: row.id}))
}

const handleSuccess: UploadProps['onError'] = (response, uploadFile, uploadFiles) => {
    router.get(route('admin.order.payment.index'));
}
const handleError: UploadProps['onError'] = (error, uploadFile, uploadFiles) => {
    console.log(error, uploadFile)
}
</script>
<style scoped>

</style>
