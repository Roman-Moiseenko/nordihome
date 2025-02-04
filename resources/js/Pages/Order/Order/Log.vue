
<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            История Заказа <span v-if="order.number">№ {{ order.number }}</span>
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
            >
                <el-table-column prop="created_at" label="Дата" width="160"/>
                <el-table-column prop="action" label="Действие" width="160"/>
                <el-table-column prop="object" label="Объект" width="160"/>
                <el-table-column prop="value" label="Значение" width="160"/>
                <el-table-column prop="link" label="Ссылка на документа" >
                    <template #default="scope">
                        <Link v-if="scope.row.link" :href="scope.row.link" >Ссылка</Link>
                    </template>
                </el-table-column>
                <el-table-column prop="comment" label="Комментарий" show-overflow-tooltip/>
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

const tableData = ref([...props.order.logs])
</script>
