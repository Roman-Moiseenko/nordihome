<template>
    <Layout>
        <template #default>
            <Head><title>{{ title }}</title></Head>
            <el-config-provider :locale="ru">
                <h1 class="font-medium text-xl">Организации (контрагенты)</h1>
                <div class="flex">

                            <el-button type="primary" class="p-4 my-3" @click="onCreate" ref="buttonRef">
                                Создать контрагента
                                <el-icon class="ml-1"><ArrowDown /></el-icon>
                            </el-button>


                    <TableFilter :filter="filter" class="ml-auto" :count="filters.count">

                        <el-select v-model="filter.holding" placeholder="Холдинг" class="mt-1">
                            <el-option v-for="item in holdings" :key="item.id" :label="item.name"
                                       :value="item.id"/>
                        </el-select>
                        <el-select v-model="filter.name" placeholder="Ответственный" class="mt-1">
                            <el-option v-for="item in staffs" :key="item.id" :label="func.fullName(item.fullname)"
                                       :value="item.id"/>
                        </el-select>
                        <el-input v-model="filter.name" placeholder="Организация, ИНН" class="mt-1"/>

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

                        <el-table-column label="Дата" width="120">
                            <template #default="scope">
                                {{ func.date(scope.row.created_at)}}
                            </template>
                        </el-table-column>
                        <el-table-column prop="number" label="№ Документа" width="160"/>
                        <el-table-column prop="distributor_name" label="Поставщик" width="260" show-overflow-tooltip/>
                        <el-table-column prop="completed" label="Проведен" width="120">
                            <template #default="scope">
                                <Active :active="scope.row.completed"/>
                            </template>
                        </el-table-column>
                        <el-table-column prop="quantity" label="Кол-во" width="100"/>
                        <el-table-column prop="amount" label="Сумма" width="120">
                            <template #default="scope">
                                {{ func.price(scope.row.amount, scope.row.currency) }}
                            </template>
                        </el-table-column>
                        <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
                        <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>
                        <el-table-column label="Действия" align="right">
                            <template #default="scope">
                                <!--el-button
                                    size="small"
                                    type="warning"
                                    @click.stop="handleCopy(scope.row)">
                                    Copy
                                </el-button-->
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
                    :current_page="organizations.current_page"
                    :per_page="organizations.per_page"
                    :total="organizations.total"
                />

            </el-config-provider>
            <DeleteEntityModal name_entity="Контрагента" />
        </template>
    </Layout>
</template>
<script lang="ts" setup>
import Layout from "@Comp/Layout.vue";
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'

import Active from '@Comp/Elements/Active.vue'

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
    holding: props.filters.holding_id,

})

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.accounting.organization.destroy', {organization: row.id}));
}
function onCreate() {
    router.post(route('admin.accounting.organization.store'))
}
function routeClick(row) {
    router.get(route('admin.accounting.organization.show', {organization: row.id}))
}



</script>
<style scoped>

</style>
