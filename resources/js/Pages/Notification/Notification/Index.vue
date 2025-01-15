<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Уведомления Системы</h1>
        <div class="flex">
            <el-button v-if="chief" type="primary" class="p-4 my-3" @click="createButton">Создать уведомление
            </el-button>

            <TableFilter :filter="filter" class="ml-auto" :count="$props.filters.count">

                <el-select v-model="filter.event" placeholder="Событие">
                    <el-option v-for="item in events" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
                <el-checkbox v-model="filter.read" label="Не прочитанные" :checked="filter.read"/>
            </TableFilter>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                :max-height="600"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                v-loading="store.getLoading"
            >
                <el-table-column sortable prop="event" label="Событие" width="200"/>
                <el-table-column prop="message" label="Сообщение"/>
                <el-table-column sortable prop="created_at" label="Создано" width="200"/>
                <el-table-column sortable prop="read_at" label="Прочитано" width="200"/>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="scope.read_at === null"
                                   size="small"
                                   @click.stop="handleRead(scope.$index, scope.row)">
                            Read
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="$page.props.notifications.current_page"
            :per_page="$page.props.notifications.per_page"
            :total="$page.props.notifications.total"
        />
    </el-config-provider>
</template>

<script lang="ts" setup>
import {useStore} from "@Res/store.js"
import {Head, Link, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import TableFilter from '@Comp/TableFilter.vue'
import {defineProps, reactive, ref} from "vue";

const props = defineProps({
    notifications: Object,
    chief: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Уведомления',
    },
    filters: Array,
    events: Array,
})
const store = useStore();
const Loading = ref(false)
const tableData = ref([...props.notifications.data])
const filter = reactive({
    event: props.filters.event,
    read: props.filters.read,
})

interface IRow {
    active: number,
    read_at: String,
}
const tableRowClassName = ({row, rowIndex}: { row: IRow }) => {
    if (row.read_at === null) {
        return 'warning-row'
    }
    return ''
}
function createButton() {
    router.get(route('admin.notification.notification.create'))
}
function handleRead(index, row) {
    router.visit(route('admin.notification.notification.read', {notification: row.id}), {
        method: 'post',
    });
}
</script>



