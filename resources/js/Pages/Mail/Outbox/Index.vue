<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Исходящие письма</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="createButton">Создать письмо</el-button>

            <TableFilter :filter="filter" class="ml-auto" :count="$props.filters.count">
                <el-input v-model="filter.email" placeholder="Email"/>
            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                :max-height="600"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column label="Получатели">
                    <template #default="scope">
                        <div v-for="item in scope.row.emails">{{ item }}</div>
                    </template>
                </el-table-column>
                <el-table-column  prop="subject" label="Тема"/>
                <el-table-column prop="attachments" label="Вложения" width="120"/>
                <el-table-column  prop="created_at" label="Создано"/>
                <el-table-column  prop="sent_at" label="Отправлено"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.sent"
                            size="small"
                            @click.stop="handleEdit(scope.row)">
                            Edit
                        </el-button>
                        <el-button v-if="!scope.row.sent"
                                   size="small"
                                   @click.stop="handleSend(scope.$index, scope.row)">
                            Send
                        </el-button>
                        <el-button v-if="scope.row.sent"
                                   size="small"
                                   @click.stop="handleRepeat(scope.$index, scope.row)">
                            Repeat
                        </el-button>
                        <el-button v-if="!scope.row.sent"
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
            :current_page="$page.props.outboxes.current_page"
            :per_page="$page.props.outboxes.per_page"
            :total="$page.props.outboxes.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="письмо" />
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import { useStore } from "@Res/store.js"
import {Head, Link, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import TableFilter from '@Comp/TableFilter.vue'

const props = defineProps({
    outboxes: Object,
    title: {
        type: String,
        default: 'Исходящая почта',
    },
    filters: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const Loading = ref(false)
const tableData = ref([...props.outboxes.data])
const filter = reactive({
    email: props.filters.email,
})

interface IRow {
    active: number
}
const tableRowClassName = ({row, rowIndex}: {row: IRow }) => {
    if (row.active === false) {
        return 'warning-row'
    }
    return ''
}
function handleEdit(row) {
    router.get(route('admin.mail.outbox.edit', {outbox: row.id}))
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.mail.outbox.destroy', {outbox: row.id}));
}
function createButton() {
    router.get(route('admin.mail.outbox.create'))
}
function routeClick(row) {
    router.get(route('admin.mail.outbox.show', {outbox: row.id}))
}
function handleRepeat(index, row) {
    router.post(route('admin.mail.outbox.repeat', {outbox: row.id}));
}
function handleSend(index, row) {
    router.post(route('admin.mail.outbox.send', {outbox: row.id}));
}

</script>
