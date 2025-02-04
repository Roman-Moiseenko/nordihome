
<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            История Заказа <span v-if="order.number">№ {{ order.number }}</span>
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="created_at" label="Дата" width="160">
                    <template #default="scope">
                        {{ func.datetime(scope.row.created_at)}}
                    </template>
                </el-table-column>
                <el-table-column prop="action" label="Действие" width="260"/>
                <el-table-column prop="object" label="Объект/Дата" width="180"/>
                <el-table-column prop="value" label="Значение" width="260"/>
                <el-table-column prop="link" label="Ссылка на документ" >
                    <template #default="scope">
                        <Link type="primary" v-if="scope.row.link" :href="scope.row.link" >Ссылка</Link>
                    </template>
                </el-table-column>
                <el-table-column prop="staff" label="Ответственный" >
                    <template #default="scope">
                        {{ func.fullName(scope.row.staff.fullname)}}
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
    order: Object,
    title: {
        type: String,
        default: 'История заказа',
    },
})
console.log(props.order.logs)
const tableData = ref([...props.order.logs])
</script>
