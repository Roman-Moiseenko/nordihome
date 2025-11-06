<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Рубрики</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить рубрику
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Рубрика" width="280" show-overflow-tooltip/>
                <el-table-column prop="title" label="Заголовок" width="" />
                <el-table-column prop="template" label="Шаблон" width="120" />
                <el-table-column prop="posts" label="Записи" width="120"/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
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

        <DeleteEntityModal name_entity="Рубрику"/>

        <el-dialog v-model="dialogCreate" title="Рубрика" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название рубрики" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder=""/>
                </el-form-item>
                <el-form-item label="Ссылка" label-position="top" class="mt-3">
                    <el-input v-model="form.slug" placeholder="Заполнится автоматически" clearable/>
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
    categories: Array,
    title: {
        type: String,
        default: 'Сайт. Рубрики',
    },
    templates: Array,
})

const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.categories])
const form = reactive({
    name: null,
    slug: null,
    template: null,
})

function savePage() {
    router.visit(route('admin.page.post-category.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogCreate.value = false;
        }
    })
}

function routeClick(row) {
    router.get(route('admin.page.post-category.show', {category: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.post-category.destroy', {category: row.id}));
}
</script>
