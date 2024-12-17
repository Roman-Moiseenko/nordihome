<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сотрудники компании</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить Сотрудника
            </el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-select v-model="filter.role" placeholder="Роль">
                    <el-option v-for="item in roles" :key="item.value" :value="item.value" :label="item.label"/>
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
                <el-table-column prop="image" label="Фото" width="80">
                    <template #default="scope">
                        <img :src="scope.row.photo" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="ФИО" width="280">
                    <template #default="scope">
                        {{ func.fullName(scope.row.fullname) }}
                    </template>
                </el-table-column>
                <el-table-column prop="role_name" label="Роль" width="180" align="center"/>
                <el-table-column prop="post" label="Должность" width="180" align="center"/>
                <el-table-column prop="phone" label="Телефон" width="180" align="center">
                    <template #default="scope">
                        {{ func.phone(scope.row.phone) }}
                    </template>
                </el-table-column>
                <el-table-column prop="telegram_user_id" label="Телеграм" width="180" align="center"/>
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
            :current_page="staffs.current_page"
            :per_page="staffs.per_page"
            :total="staffs.total"
        />
        <DeleteEntityModal name_entity="Сотрудника"/>

        <el-dialog v-model="dialogCreate" title="Новый сотрудник" width="500">
            <el-form label-width="auto">

                <el-form-item label="Фамилия" class="mt-3">
                    <el-input v-model="form.surname" placeholder="Фамилия"/>
                    <div v-if="errors.surname" class="text-red-700">{{ errors.surname }}</div>
                </el-form-item>
                <el-form-item label="Имя" class="mt-3">
                    <el-input v-model="form.firstname" placeholder="Имя"/>
                    <div v-if="errors.firstname" class="text-red-700">{{ errors.firstname }}</div>
                </el-form-item>
                <el-form-item label="Отчество" class="mt-3">
                    <el-input v-model="form.secondname" placeholder="Отчество"/>
                    <div v-if="errors.secondname" class="text-red-700">{{ errors.secondname }}</div>
                </el-form-item>
                <el-divider/>
                <el-form-item label="Телефон" class="mt-3">
                    <el-input v-model="form.phone" :formatter="val => func.MaskPhone(val)"/>
                    <div v-if="errors.phone" class="text-red-700">{{ errors.phone }}</div>
                </el-form-item>
                <el-form-item label="Email" class="mt-3">
                    <el-input v-model="form.email" :formatter="val => func.MaskEmail(val)"/>
                    <div v-if="errors.email" class="text-red-700">{{ errors.email }}</div>
                </el-form-item>
                <el-form-item label="Телеграм" class="mt-3">
                    <el-input v-model="form.telegram_user_id" :formatter="val => func.MaskInteger(val)"/>
                    <div v-if="errors.telegram_user_id" class="text-red-700">{{ errors.telegram_user_id }}</div>
                </el-form-item>
                <el-form-item label="Логин" class="mt-3">
                    <el-input v-model="form.name" :formatter="val => func.MaskLogin(val)"/>
                    <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                </el-form-item>
                <el-form-item label="Пароль" class="mt-3">
                    <el-input v-model="form.password"/>
                    <div v-if="errors.password" class="text-red-700">{{ errors.password }}</div>
                </el-form-item>
                <el-divider/>
                <el-form-item label="Должность" class="mt-3">
                    <el-input v-model="form.post"/>
                    <div v-if="errors.post" class="text-red-700">{{ errors.post }}</div>
                </el-form-item>
                <el-form-item label="Роль" class="mt-3">
                    <el-select v-model="form.role">
                        <el-option v-for="item in roles" :value="item.value" :label="item.label" />
                    </el-select>
                    <div v-if="errors.role" class="text-red-700">{{ errors.role }}</div>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveStaff">Сохранить</el-button>
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
    staffs: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Сотрудники список',
    },
    filters: Array,
    roles: Array,
})
console.log(props.staffs)
const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.staffs.data])
const filter = reactive({
    role: props.filters.role,
})


const form = reactive({
    surname: null,
    firstname: null,
    secondname: null,
    phone: null,
    telegram_user_id: null,
    email: null,
    name: null,
    role: null,
    post: null,
})


function saveStaff() {
    router.visit(route('admin.staff.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogCreate.value = false;
        }
    })

}

function routeClick(row) {
    router.get(route('admin.staff.show', {staff: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.staff.destroy', {staff: row.id}));
}
</script>
