<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Страницы</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить страницу
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Страница" width="280" show-overflow-tooltip/>
                <el-table-column prop="title" label="Заголовок" width="" />
                <el-table-column prop="template" label="Шаблон" width="120" show-overflow-tooltip/>
                <el-table-column prop="menu" label="Меню" width="100" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.menu" />
                    </template>
                </el-table-column>
                <el-table-column prop="published" label="Опубликована" width="130" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button size="small" type="primary" dark @click.stop="onUp(scope.row)">
                            <i class="fa-light fa-chevron-up"></i>
                        </el-button>
                        <el-button size="small" type="primary" dark @click.stop="onDown(scope.row)">
                            <i class="fa-light fa-chevron-down"></i>
                        </el-button>

                        <el-button size="small"
                                   :type="scope.row.published ? 'warning' : 'success'"
                                   @click.stop="onToggle(scope.row)"
                        >
                            {{ scope.row.published ? 'Draft' : 'Active' }}
                        </el-button>
                        <el-button v-if="!scope.row.published"
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

        <DeleteEntityModal name_entity="Страницу"/>

        <el-dialog v-model="dialogCreate" title="Страница" width="500">
            <el-form label-width="auto">
                <el-form-item label="Имя страницы" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Внутреннее"/>
                </el-form-item>
                <el-form-item label="Ссылка" label-position="top" class="mt-3">
                    <el-input v-model="form.slug" placeholder="Заполнится автоматически" clearable/>
                </el-form-item>
                <el-form-item label="Родительская страница" label-position="top" class="mt-3">
                    <el-select v-model="form.parent_id" filterable clearable>
                        <el-option v-for="item in parent_pages" :value="item.id" :label="item.name" />
                    </el-select>
                </el-form-item>

                <el-form-item label="Шаблон" label-position="top" class="mt-3">
                    <el-select v-model="form.template" filterable clearable>
                        <el-option v-for="item in templates" :value="item.value" :label="item.label" />
                    </el-select>
                </el-form-item>




            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="savePage">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
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

const props = defineProps({
    pages: Array,
    title: {
        type: String,
        default: 'Сайт. Страницы',
    },
    templates: Array,
    parent_pages: Array,
})

const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.pages])
const form = reactive({
    name: null,
    slug: null,
    template: null,
    parent_id: null,
})

function savePage() {
    router.visit(route('admin.page.page.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogCreate.value = false;
        }
    })
}
function onToggle(row) {
    router.visit(route('admin.page.page.toggle', {page: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function routeClick(row) {
    router.get(route('admin.page.page.show', {page: row.id}))
}
function onUp(row) {
    router.visit(route('admin.page.page.up', {page: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onDown(row) {
    router.visit(route('admin.page.page.down', {page: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.page.destroy', {page: row.id}));
}
</script>
