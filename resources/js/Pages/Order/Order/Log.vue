
<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            История Заказа
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="created_at" label="Дата" width="160">
                    <template #default="scope">
                        {{ scope.row.createdAt}}
                    </template>
                </el-table-column>
                <el-table-column prop="action" label="Действие" width="260"/>
                <el-table-column prop="object" label="Объект/Дата" width="180"/>
                <el-table-column prop="value" label="Значение" width="200"/>
                <el-table-column prop="old" label="Предыдущее" width="200"/>
                <el-table-column prop="link" label="Ссылка на документ" >
                    <template #default="scope">
                        <Link type="primary" v-if="scope.row.link" :href="scope.row.link" >Ссылка</Link>
                    </template>
                </el-table-column>
                <el-table-column prop="staff" label="Ответственный" >
                    <template #default="scope">
                        {{ scope.row.staffName}}
                    </template>
                </el-table-column>

            </el-table>
        </h1>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {defineProps, ref} from "vue";
import {Head, Link} from "@inertiajs/vue3";
import {func} from "@Res/func.js";

const props = defineProps({
   // order: Object,
    title: {
        type: String,
        default: 'История заказа',
    },
    logs: Array,
})

const tableData = ref([...props.logs])
</script>
