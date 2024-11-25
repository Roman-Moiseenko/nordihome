<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Действия по расписанию</h1>

        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="created_at" label="Дата" width="120">
                    <template #default="scope">
                        {{ func.date(scope.row.created_at) }}
                    </template>
                </el-table-column>
                <el-table-column prop="event" label="Событие" width="420"/>

            </el-table>
        </div>
        <pagination
            :current_page="crons.current_page"
            :per_page="crons.per_page"
            :total="crons.total"
        />

    </el-config-provider>
</template>
<script lang="ts" setup>
import { ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'


const props = defineProps({
    crons: Object,
    title: {
        type: String,
        default: 'Действия по расписанию',
    },
})
const store = useStore();

const tableData = ref([...props.crons.data])
function routeClick(row) {
    router.get(route('admin.analytics.cron.show', {cron: row.id}))
}
</script>

