<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Виджеты</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Добавить виджет
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Виджет" width="280" show-overflow-tooltip/>
                <el-table-column prop="count" label="Кол-во элементов" width="180" />
                <el-table-column prop="published" label="Баннер" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.banner" />
                    </template>
                </el-table-column>
                <el-table-column prop="template" label="Шаблон"  align="center" />
                <el-table-column prop="published" label="Опубликован" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button
                            size="small"
                            :type="scope.row.active ? 'warning' : 'success'"
                            @click.stop="onToggle(scope.row)"
                        >
                            {{ scope.row.active ? 'Draft' : 'Active' }}
                        </el-button>
                        <el-button v-if="!scope.row.active"
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

        <DeleteEntityModal name_entity="Виджет"/>

        <el-dialog v-model="dialogCreate" title="Виджет" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder=""/>
                </el-form-item>
                <el-form-item label="Шаблон" label-position="top" class="mt-3">
                    <el-select v-model="form.template" >
                        <el-option v-for="item in templates" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveWidget">Сохранить</el-button>
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
    widgets: Array,
    title: {
        type: String,
        default: 'Сайт. Виджеты',
    },
    templates: Array,
})
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.widgets])
const form = reactive({
    id: null,
    name: null,
    templates: null,
})

function saveWidget() {
    router.visit(route('admin.page.widget.store' ), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogCreate.value = false
        },
    })
}
function onToggle(row) {
    router.visit(route('admin.page.widget.toggle', {widget: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function routeClick(row) {
   router.get(route('admin.page.widget.show', {widget: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.widget.destroy', {widget: row.id}));
}
</script>
