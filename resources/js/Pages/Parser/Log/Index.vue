<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
    <div class="mt-2 p-5 bg-white rounded-md">
        <el-table
            :data="tableData"
            header-cell-class-name="nordihome-header"
            style="width: 100%; cursor: pointer;"
            :row-class-name="classes.TableRead"
            row-key="id"
            @row-click="rowClick"
        >

            <el-table-column prop="date" label="Дата" width="140" />
            <el-table-column prop="new" label="Новые" width="120"/>
            <el-table-column prop="change" label="Цена" width="120"/>
            <el-table-column prop="del" label="Удаленные" width="120"/>
            <el-table-column label="Действия" align="right">
                <template #default="scope">
                    <el-button v-if="!scope.row.read"
                               size="small"
                               type="success"
                               @click.stop="onRead(scope.row)"
                    >
                        Read
                    </el-button>
                </template>
            </el-table-column>

        </el-table>
    </div>
    <pagination
        :current_page="logs.current_page"
        :per_page="logs.per_page"
        :total="logs.total"
    />
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import Pagination from "@Comp/Pagination.vue";
import {route} from "ziggy-js";
import Active from "@Comp/Elements/Active.vue";
import {ref} from "vue";
import TableFilter from "@Comp/TableFilter.vue";
import {classes} from "@Res/className";


const props = defineProps({
    logs: Object,
    title: {
        type: String,
        default: 'История парсера',
    },
})
const tableData = ref([...props.logs.data])

function rowClick(row) {
    router.get(route('admin.parser.log.show', {parser_log: row.id}))
}
function onRead(row) {
    router.post(route('admin.parser.log.read', {parser_log: row.id}))
}
</script>
