<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Обратная связь. Формы</h1>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
            >
                <el-table-column prop="created_at" label="Дата" width="160" />
                <el-table-column prop="url" label="Страница" width="160" show-overflow-tooltip/>
                <el-table-column prop="widget" label="Форма" />
                <el-table-column prop="data" label="Данные">
                    <template #default="scope">
                        {{ scope.row.data }}
                    </template>
                </el-table-column>
                <el-table-column prop="lead" label="Лид" width="180"/>
            </el-table>
        </div>
        <pagination
            :current_page="forms.current_page"
            :per_page="forms.per_page"
            :total="forms.total"
        />
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";

import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";
import Pagination from "@Comp/Pagination.vue";

const props = defineProps({
    forms: Array,

    title: {
        type: String,
        default: 'Сайт. Страницы',
    },

})

const tableData = ref([...props.forms.data])


</script>
