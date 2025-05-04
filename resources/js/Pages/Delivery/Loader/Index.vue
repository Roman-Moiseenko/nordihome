<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Заказы на сборку</h1>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="classes.TableCompleted"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="number" label="Номер" width="80"/>
                <el-table-column prop="recipient" label="Клиент" width="260" show-overflow-tooltip>
                    <template #default="scope">
                        <div class="font-medium text-sm">{{ func.fullName(scope.row.recipient) }}</div>
                        <div class="text-slate-700 text-xs">{{ func.phone(scope.row.phone) }}</div>
                    </template>
                </el-table-column>
                <el-table-column prop="type_text" label="Доставка" width="180"/>
                <el-table-column prop="status_text" label="Статус" width="120">
                    <template #default="scope">
                        <el-tag :type="statusType(scope.row.status)">{{ scope.row.status_text }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="work" label="Ответственный" show-overflow-tooltip>
                    <template #default="scope">
                        <div v-for="worker in scope.row.workers">
                            <el-tag>{{ worker.work }}</el-tag>
                            {{ func.fullName(worker.fullname) }}
                            <el-tooltip v-if="scope.row.status.is_assembling" content="Отменить" placement="top-start" effect="dark">
                                <el-button type="danger" size="small" @click.stop="delLoader(scope.row, worker.id)">
                                    <i class="fa-light fa-xmark"></i>
                                </el-button>
                            </el-tooltip>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip width="120"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-popover v-if="scope.row.status.is_assembly"
                                    :visible="scope.row.visible_assembly"
                                    placement="bottom-start" :width="246">
                            <template #reference>
                                <el-button type="primary" class="p-4 mr-2"
                                           @click.stop="scope.row.visible_assembly = !scope.row.visible_assembly">
                                    Назначить
                                    <el-icon class="ml-1">
                                        <ArrowDown/>
                                    </el-icon>
                                </el-button>
                            </template>
                            <el-select v-model="worker_id">
                                <el-option v-for="item in works" :value="item.id"
                                           :label="func.fullName(item.fullname)"/>
                            </el-select>
                            <div class="mt-2">
                                <el-button @click="scope.row.visible_assembly = false">Отмена</el-button>
                                <el-button @click="scope.row.visible_assembly = false; setLoader(scope.row)"
                                           type="primary">Назначить
                                </el-button>
                            </div>
                        </el-popover>
                        <el-button type="success"
                                   v-if="(scope.row.status.is_assembling || scope.row.status.is_assembled) && !scope.row.is_delivery"
                                   @click.stop="onComplete(scope.row)">
                            Выдать
                        </el-button>
                        <el-button v-if="scope.row.status.is_assembling && scope.row.is_delivery"
                                   @click.stop="handleAssembled(scope.row)">
                            Собран
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="expenses.current_page"
            :per_page="expenses.per_page"
            :total="expenses.total"
        />

        <el-dialog v-model="dialogHonest" title="Добавьте маркировку для указанных товаров">
            <div v-for="item in honest_signs">
                <el-tag>{{ item.name }}</el-tag>
                <el-input v-model="item.signs" type="textarea" :rows="item.quantity" />
            </div>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogHonest = false">Отмена</el-button>
                    <el-button type="primary" @click="setHonest">Выдать</el-button>
                </div>
            </template>
        </el-dialog>

    </el-config-provider>
    <DeleteEntityModal name_entity="Заказ поставщику"/>
</template>
<script lang="ts" setup>
import {ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import {classes} from "@Res/className"
import { IHonestItem } from "@Res/interface"

const props = defineProps({
    expenses: Object,
    title: {
        type: String,
        default: 'Заказы на сборку',
    },
    works: Array,
})
const store = useStore();
const tableData = ref([...props.expenses.data.map(item => {
    item.visible_assembly = ref(false)
    return item
})])
const worker_id = ref(null)

function routeClick(row) {
    router.get(route('admin.order.expense.show', {expense: row.id}))
}

function setLoader(row) {
    router.visit(route('admin.delivery.set-loader', {expense: row.id}), {
        method: "post",
        data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: false,

    })
}

function delLoader(row, id) {
    router.visit(route('admin.delivery.del-loader', {expense: row.id}), {
        method: "post",
        data: {worker_id, id},
        preserveScroll: true,
        preserveState: false,

    })
}

const honest_signs = ref<IHonestItem[]>([]);
const dialogHonest = ref(false)
function handleAssembled(row) {

    row.items.forEach(function (item) {
        if (item.is_honest === true) {
            honest_signs.value.push({
                id: item.id,
                name: item.product.name,
                quantity: item.quantity,
                signs: null,
            })
        }
    })
    if (honest_signs.value.length > 0) dialogHonest.value = true;
    console.log(honest_signs.value)
}

function setHonest() {
   console.log(honest_signs.value)
}
function onAssembled(row) {
    router.visit(route('admin.delivery.assembled', {expense: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function onComplete(row) {
    router.visit(route('admin.delivery.completed', {expense: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function statusType(status) {
    if (status.is_assembly) return 'danger'
    if (status.is_assembling) return 'warning'
    if (status.is_assembled) return 'primary'
    if (status.is_delivery) return 'primary'
    if (status.is_completed) return 'success'
    return 'info'
}

</script>
<style scoped>

</style>
