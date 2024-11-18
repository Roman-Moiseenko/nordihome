<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Организации (контрагенты)</h1>
        <div class="flex my-3">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать контрагента
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="create.inn" placeholder="ИНН" class="mt-1"/>
                <el-input v-model="create.bik" placeholder="БИК" class="mt-1"/>
                <el-input v-model="create.account" placeholder="Расчетный счет" class="mt-1"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <el-button type="primary" plain >Холдинги</el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-select v-model="filter.holding" placeholder="Холдинг" class="mt-1">
                    <el-option v-for="item in holdings" :key="item.id" :label="item.name"
                               :value="item.id"/>
                </el-select>
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

                <el-table-column prop="short_name" label="Организация">
                    <template #default="scope">
                        {{ scope.row.short_name }} <span v-if="scope.row.holding_id" class="font-medium">({{ scope.row.holding.name }})</span>
                    </template>
                </el-table-column>
                <el-table-column prop="inn" label="ИНН" width="160"/>
                <el-table-column prop="chief" label="Руководитель" show-overflow-tooltip>
                    <template #default="scope">
                        {{ func.fullName(scope.row.chief) }}
                    </template>
                </el-table-column>
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
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'

const props = defineProps({
    organizations: Object,
    title: {
        type: String,
        default: 'Список контрагентов',
    },
    filters: Array,
    holdings: Array,
})
const store = useStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.organizations.data])
const filter = reactive({
    name: props.filters.name,
    holding: props.filters.holding,

})
const create = reactive({
    inn: null,
    bik: null,
    account: null,
})
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.organization.destroy', {organization: row.id}));
}
function createButton() {
    router.post(route('admin.accounting.organization.store', create))
}
function routeClick(row) {
    router.get(route('admin.accounting.organization.show', {organization: row.id}))
}
</script>
