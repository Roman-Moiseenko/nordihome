<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Входящие</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="handleLoad">Получить почту</el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="$props.filters.count">
                <el-input v-model="filter.from" placeholder="Email, Имя"/>
                <el-select v-model="filter.box" placeholder="Почта" class="mt-1">
                    <el-option v-for="item in boxes" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
                <el-checkbox v-model="filter.read" label="Непрочитанные" :checked="filter.read"/>
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
                <el-table-column sortable prop="created_at" label="Получено" width="160" />
                <el-table-column sortable prop="from" label="От кого" />
                <el-table-column prop="subject" label="Тема"/>
                <el-table-column prop="attachments" label="Вложения" width="120"/>
                <el-table-column sortable prop="read_at" label="Прочитано" width="160" />
                <el-table-column sortable prop="box" label="Почта" width="100"/>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
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
            :current_page="$page.props.inboxes.current_page"
            :per_page="$page.props.inboxes.per_page"
            :total="$page.props.inboxes.total"
        />
    </el-config-provider>
    <DeleteEntityModal name_entity="входящее письмо" />
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import { useStore } from "@Res/store.js"
import {Head, Link, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import TableFilter from '@Comp/TableFilter.vue'

const props = defineProps({
    inboxes: Object,
    title: {
        type: String,
        default: 'Входящая почта',
    },
    filters: Array,
    boxes: Array,
})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const Loading = ref(false)
const tableData = ref([...props.inboxes.data])
const filter = reactive({
    from: props.filters.from,
    read: props.filters.read,
    box: props.filters.box,
})

interface IRow {
    read: number
}
const tableRowClassName = ({row, rowIndex}: {row: IRow }) => {
    if (row.read === false) {
        return 'warning-row'
    }
    return ''
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.mail.inbox.destroy', {inbox: row.id}));
}
function routeClick(row) {
    router.get(route('admin.mail.inbox.show', {inbox: row.id}))
}
function handleLoad() {
    router.get(route('admin.mail.inbox.load'))
}

</script>

