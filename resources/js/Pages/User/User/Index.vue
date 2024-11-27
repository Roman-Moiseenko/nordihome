<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Клиенты</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить Клиента
            </el-button>

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Имя,Тел.,Email,ИНН,Компания" class="mt-1"/>
                <el-input v-model="filter.address" placeholder="Адрес" class="mt-1"/>
                <el-select v-model="filter.client" placeholder="Тип цен" class="mt-1">
                    <el-option v-for="item in type_pricing" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
                <el-checkbox v-model="filter.wait" label="Не подтвержден" class="mt-1" :checked="filter.wait"/>
            </TableFilter>
        </div>

        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Клиент" width="" show-overflow-tooltip>
                    <template #default="scope">
                        <div class="font-medium text-sm">{{ scope.row.name }}</div>
                        <div class="text-slate-700 text-xs">{{ func.phone(scope.row.phone) }}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="pricing" label="Цена" width="100"/>
                <el-table-column prop="last_order" label="Последний заказ" width="100">
                    <template #default="scope">
                        <el-tag v-if="scope.row.last_order" :type="scope.row.last_order.type" round>
                            {{ scope.row.last_order.text }}
                        </el-tag>
                        <el-tag v-else type="info" round>нет</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="quantity" label="Кол-во заказов" width="160" align="center"/>
                <el-table-column prop="amount" label="Общая сумма" width="200" align="center">
                    <template #default="scope">
                        {{ func.price(scope.row.amount) }}
                    </template>
                </el-table-column>
                <el-table-column prop="address.region" label="Регион"/>
                <el-table-column prop="active" label="Подтвержден" width="120">
                    <template #default="scope">
                        <Active :active="scope.row.active"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right" width="160">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="success"
                            @click.stop="createOrder(scope.row)">
                            Order
                        </el-button>
                        <el-button v-if="!scope.row.active"
                            size="small"
                            type="primary"
                            @click.stop="onActive(scope.row)">
                            Activate
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <pagination
            :current_page="users.current_page"
            :per_page="users.per_page"
            :total="users.total"
        />
        <el-dialog v-model="dialogCreate" title="Новый клиент" width="500">
            <el-form>
                <el-form-item>
                    <el-input v-model="formCreate.phone" placeholder="Телефон" :formatter="val => func.MaskPhone(val)"/>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="formCreate.email" placeholder="Email"/>
                </el-form-item>
                <el-form-item>
                    <div class="flex">
                        <el-input v-model="formCreate.fullname.surname" placeholder="Фамилия"/>
                        <el-input v-model="formCreate.fullname.firstname" placeholder="Имя"/>
                        <el-input v-model="formCreate.fullname.secondname" placeholder="Отчество"/>
                    </div>
                </el-form-item>
                <el-form-item>
                    <div class="flex">
                        <el-input v-model="formCreate.inn" placeholder="ИНН"
                                  :formatter="val => func.MaskInteger(val, 12)"/>
                        <el-input v-model="formCreate.bik" placeholder="БИК"
                                  :formatter="val => func.MaskInteger(val, 9)"/>
                    </div>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="formCreate.account" placeholder="Р/счет"
                              :formatter="val => func.MaskPhone(val, 20)"/>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveUser">Сохранить</el-button>
                </div>
            </template>

        </el-dialog>
    </el-config-provider>
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
    users: Object,
    title: {
        type: String,
        default: 'Список клиентов',
    },
    filters: Array,
    type_pricing: Array,
})
const store = useStore();
const visible_create = ref(false)
const tableData = ref([...props.users.data])
const filter = reactive({
    name: props.filters.name,
    address: props.filters.address,
    client: props.filters.client,
    wait: props.filters.wait,
})
const dialogCreate = ref(false)
const formCreate = reactive({
    email: null,
    phone: null,
    fullname: {
        surname: null,
        firstname: null,
        secondname: null,
    },
    inn: null,
    bik: null,
    account: null,
})

function saveUser() {
    router.visit(route('admin.user.create'), {
        method: "post",
        data: formCreate,
        onSuccess: page => {
            dialogCreate.value = false
        }
    })
}

interface IRow {
    active: any
}

const tableRowClassName = ({row}: { row: IRow }) => {
    if (row.active === 0) {
        return 'warning-row'
    }
    return ''
}

function onActive(row) {
    router.visit(route('admin.user.verify', {user: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}

function createOrder(row) {
    router.post(route('admin.order.store', {user_id: row.id}))
}

function routeClick(row) {
    router.get(route('admin.user.show', {user: row.id}))
}

function diffDate(date: any) {
    let today = Date.parse(new Date());
    let old = Date.parse(date);

    return Math.round((today - old) / (1000 * 60 * 60 * 24))
}
</script>

