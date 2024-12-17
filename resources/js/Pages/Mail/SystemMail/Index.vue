<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Системные письма</h1>
        <div class="flex">
            <TableFilter :filter="filter" class="ml-auto" :count="$props.filters.count">
                <el-select v-model="filter.mailable" placeholder="Событие">
                    <el-option v-for="item in mailables" :key="item.value" :label="item.label" :value="item.value"/>
                </el-select>
            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                :max-height="600"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column sortable prop="mailable" label="Служба"/>
                <el-table-column prop="title" label="Заголовок"/>
                <el-table-column sortable prop="user" label="Получатель"/>
                <el-table-column prop="created_at" label="Отправлено"/>
                <el-table-column prop="count_attachments" label="Вложения" width="120"/>
                <el-table-column prop="count" label="Отправок"/>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            @click.stop="handleRepeat(scope.$index, scope.row)">
                            Repeat
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <pagination
            :current_page="$page.props.systemMails.current_page"
            :per_page="$page.props.systemMails.per_page"
            :total="$page.props.systemMails.total"
        />
    </el-config-provider>

</template>

<script lang="ts" setup>
import {useStore} from "@Res/store.js"
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import TableFilter from '@Comp/TableFilter.vue'
import {defineProps, reactive, ref} from "vue";

const props = defineProps({
    systemMails: Object,
    title: {
        type: String,
        default: 'Системная почта',
    },
    filters: Array,
    mailables: Array,
})
const store = useStore();
const Loading = ref(false)
const tableData = ref([...props.systemMails.data])
const filter = reactive({
    mailable: props.filters.mailable,
})

function routeClick(row) {
    router.get(route('admin.mail.system.show', {system: row.id}))
}
function handleRepeat(index, row) {
    router.visit(route('admin.mail.system.repeat', {system: row.id}), {
        method: 'post'
    });
}

</script>


