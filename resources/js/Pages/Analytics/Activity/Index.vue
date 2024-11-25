<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Действия сотрудников</h1>
        <div class="flex">

            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-select v-model="filter.staff" placeholder="Сотрудник" class="mt-1">
                    <el-option v-for="item in staffs" :key="item.id" :label="func.fullName(item.fullname)" :value="item.id"/>
                </el-select>
            </TableFilter>
        </div>

        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"

                v-loading="store.getLoading"
            >
                <el-table-column prop="created_at" label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="staff.id" label="Сотрудник" width="220">
                    <template #default="scope">
                        {{ func.fullName(scope.row.staff.fullname) }}
                    </template>
                </el-table-column>
                <el-table-column prop="action" label="Действие" width="260"/>
                <el-table-column prop="url" label="Ссылка" width="260" align="center"/>
                <el-table-column prop="request_params" label="Параметры"/>
            </el-table>
        </div>
        <pagination
            :current_page="activities.current_page"
            :per_page="activities.per_page"
            :total="activities.total"
        />

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
    activities: Object,
    title: {
        type: String,
        default: 'Действия сотрудников',
    },
    filters: Array,
    staffs: Array,
})
const store = useStore();

const tableData = ref([...props.activities.data])
const filter = reactive({
    staff: props.filters.staff,

})

</script>

