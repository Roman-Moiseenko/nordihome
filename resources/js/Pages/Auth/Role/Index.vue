<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Управление ролями</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить роль
            </el-button>
            <TableFilter :filter="filter" class="ml-auto">
                <el-select v-model="filter.type" placeholder="Тип роли" @change="onFilterChange">
                    <el-option value="custom" label="Пользовательские"/>
                    <el-option value="system" label="Системные"/>
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
                <el-table-column prop="name" label="Имя" width="200"/>
                <el-table-column prop="description" label="Описание" min-width="250"/>
                <el-table-column label="Кол-во разрешений" width="180" align="center">
                    <template #default="scope">
                        <el-tag>{{ scope.row.permissions?.length ?? 0 }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="Системная" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.is_system"/>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right" width="120">
                    <template #default="scope">
                        <el-button
                            v-if="!scope.row.is_system"
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
        <DeleteEntityModal name_entity="Роль"/>

        <AuthRoleCreate v-model="dialogCreate" :errors="errors"/>

    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import {route} from "ziggy-js";

import AuthRoleCreate from "@Comp/Auth/Role/Create.vue"

const props = defineProps({
    roles: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Роли список',
    },
    filters: Object,
})
const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.roles])

const filter = reactive({
    type: props.filters?.type ?? 'custom',
})

function onFilterChange() {
    router.get(route('admin.role.index', {type: filter.type}), {
        preserveState: true,
        preserveScroll: true,
    })
}

function routeClick(row) {
    router.get(route('admin.role.show', {role: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.role.destroy', {role: row.id}));
}
</script>
