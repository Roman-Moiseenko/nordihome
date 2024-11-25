<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">{{ cron.event }} от {{ cron.created_at}}</h1>

        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                v-loading="store.getLoading"
            >
                <el-table-column prop="object" label="Объект" width="220"/>
                <el-table-column prop="action" label="Действие" width=""/>
                <el-table-column prop="value" label="Значение" width=""/>
            </el-table>
        </div>

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
    cron: Object,
    title: {
        type: String,
        default: 'Действия по расписанию',
    },
})
const store = useStore();

const tableData = ref([...props.cron.items])

</script>

