<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Организации (контрагенты)</h1>
        <div class="flex my-3">
            <el-button type="primary" class="p-4" @click="showCreate = true" ref="buttonRef">
                Создать контрагента
            </el-button>
            <CreateOrganization  :show="showCreate" @create:id="onCreate" @create:cancel="showCreate = value" />
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Организация, ИНН, email, тел" class="mt-1"/>
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

                <el-table-column prop="shortName" label="Организация"/>
                <el-table-column prop="inn" label="ИНН" width="160"/>
                <el-table-column prop="chief" label="Руководитель" show-overflow-tooltip/>
                <el-table-column prop="types" label="Функционал"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <!--el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button-->
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="organizations.current_page"
            :per_page="organizations.per_page"
            :total="organizations.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Контрагента" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import CreateOrganization from "@Comp/Search/CreateOrganization.vue";

const props = defineProps({
    organizations: Object,
    title: {
        type: String,
        default: 'Список контрагентов',
    },
    filters: Object,
})

const showCreate = ref(null)
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.organizations.data])
const filter = reactive({
    name: props.filters.name ?? null,
})

function onCreate(id) {
    router.get(route('admin.accounting.organization.show', {id: id}))
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.organization.destroy', {id: row.id}));
}
function routeClick(row) {
    router.get(route('admin.accounting.organization.show', {id: row.id}))
}
</script>
