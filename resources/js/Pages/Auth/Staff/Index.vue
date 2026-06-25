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
                    <el-option v-for="item in useAuth.positions" :key="item.value" :value="item.value" :label="item.label"/>
                </el-select>

            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="Фото" width="80">
                    <template #default="scope">
                        <img :src="scope.row.photo" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="fullName" label="ФИО" width="280"/>
                <el-table-column prop="positions" label="Роли" width="180" align="center"/>
                <el-table-column prop="workPhone" label="Телефон" width="180" align="center">
                    <template #default="scope">
                        {{ func.phone(scope.row.workPhone) }}
                    </template>
                </el-table-column>
                <el-table-column prop="telegramChatId" label="Телеграм" width="180" align="center"/>
                <el-table-column prop="isActive" label="Активен" width="180" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.isActive"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small"
                                   :type="!scope.row.isActive ? 'success' : 'warning'"
                        >
                            {{ !scope.row.isActive ? 'Active' : 'Draft' }}
                        </el-button>
                        <el-button v-if="!scope.row.isActive"
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

        <AuthStaffCreate v-model="dialogCreate"  :errors="errors" />

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

import AuthStaffCreate from "@Comp/Auth/Staff/Create.vue"
import {useAuthStore} from "@Res/authStore";

const props = defineProps({
    staffs: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Сотрудники список',
    },
    filters: Array,
})
const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.staffs.data])
const filter = reactive({
    role: props.filters.role,
})
const useAuth = useAuthStore();
function routeClick(row) {
    router.get(route('admin.staff.show', {staff: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.staff.destroy', {staff: row.id}));
}
</script>
