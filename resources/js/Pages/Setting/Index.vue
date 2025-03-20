<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Настройки</h1>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                style="width: 100%; cursor: pointer;"
                header-cell-class-name="nordihome-header"
                :row-class-name="classes.TableActive"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column sortable prop="name" label="Название" width="240"/>
                <el-table-column sortable prop="slug" label="Ссылка" width="240"/>
                <el-table-column prop="description" label="Описание" />
            </el-table>
        </div>

        <pagination
            :current_page="props.settings.current_page"
            :per_page="props.settings.per_page"
            :total="props.settings.total"
        />
    </el-config-provider>

</template>

<script lang="ts" setup>
import { Head, router } from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import ru from 'element-plus/dist/locale/ru.mjs'
import { useStore } from "@Res/store.js"
import {defineProps, ref} from "vue";
import {classes} from "@Res/className"

const props = defineProps({
    settings: Object,
    title: {
        type: String,
        default: 'Список Классов настроек',
    }
})
const store = useStore();
const tableData = ref([...props.settings.data])

function routeClick(row) {
    router.get(route('admin.setting.' + row.slug))
}
</script>


