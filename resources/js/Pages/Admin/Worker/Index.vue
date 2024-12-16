<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Рабочие компании</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить работника
            </el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-select v-model="filter.post" placeholder="Специализация">
                    <el-option v-for="item in posts" :key="item.value" :value="item.value" :label="item.label"/>
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
                <el-table-column prop="name" label="ФИО" width="280">
                    <template #default="scope">
                        {{ func.fullName(scope.row.fullname) }}
                    </template>
                </el-table-column>
                <el-table-column label="Специализация" width="180" align="center">
                    <template #default="scope">
                        <el-tag v-if="scope.row.driver" type="info" class="ml-1">Водитель</el-tag>
                        <el-tag v-if="scope.row.loader" type="info" class="ml-1">Грузчик</el-tag>
                        <el-tag v-if="scope.row.assemble" type="info" class="ml-1">Сборщик</el-tag>
                        <el-tag v-if="scope.row.logistic" type="info" class="ml-1">Логист</el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="phone" label="Телефон" width="180" align="center">
                    <template #default="scope">
                        {{ func.phone(scope.row.phone) }}
                    </template>
                </el-table-column>
                <el-table-column prop="telegram_user_id" label="Телеграм" width="180" align="center"/>
                <el-table-column prop="storage_name" label="Склад" width="180" align="center"/>
                <el-table-column prop="active" label="Активен" width="180" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small"
                                   :type="!scope.row.active ? 'success' : 'warning'"
                        >
                            {{ !scope.row.active ? 'Active' : 'Draft' }}
                        </el-button>
                        <el-button v-if="!scope.row.active"
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
            :current_page="workers.current_page"
            :per_page="workers.per_page"
            :total="workers.total"
        />
        <DeleteEntityModal name_entity="Работника"/>


        <el-dialog v-model="dialogCreate" title="Новый работник" width="500">
            <el-form label-width="auto">
                <el-form-item label="ФИО" label-position="top" class="mt-3">
                    <div class="flex">
                        <el-input v-model="form.surname" placeholder="Фамилия"/>
                        <el-input v-model="form.firstname" placeholder="Имя"/>
                        <el-input v-model="form.secondname" placeholder="Отчество"/>
                    </div>
                </el-form-item>
                <el-form-item label="Телефон" class="mt-3">
                    <el-input v-model="form.phone" :formatter="val => func.MaskPhone(val)"/>
                </el-form-item>
                <el-form-item label="Телеграм" class="mt-3">
                    <el-input v-model="form.telegram_user_id" :formatter="val => func.MaskInteger(val)"/>
                </el-form-item>
                <el-form-item label="Специализация">
                    <el-checkbox v-model="form.driver" label="Водитель"/>
                    <el-checkbox v-model="form.loader" label="Грузчик"/>
                    <el-checkbox v-model="form.assemble" label="Сборщик"/>
                    <el-checkbox v-model="form.logistic" label="Логист"/>
                </el-form-item>
                <el-form-item label="Склад">
                    <el-select v-model="form.storage_id" clearable>
                        <el-option v-for="item in storages" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveWorker">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";
import {func} from "@Res/func";

const props = defineProps({
    workers: Array,
    title: {
        type: String,
        default: 'Рабочие список',
    },
    filters: Array,
    storages: Array,
})
const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.workers.data])
const filter = reactive({
    post: props.filters.post,
})


const form = reactive({
    surname: null,
    firstname: null,
    secondname: null,
    phone: null,
    telegram_user_id: null,
    driver: null,
    loader: null,
    assemble: null,
    logistic: null,
    storage_id: null,
})


function saveWorker() {
    router.post(route('admin.worker.store', form))
    dialogCreate.value = false;
}

function routeClick(row) {
    router.get(route('admin.worker.show', {worker: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.worker.destroy', {worker: row.id}));
}
</script>
