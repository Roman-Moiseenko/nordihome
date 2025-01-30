<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Баннеры</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onOpenDialog" ref="buttonRef">
                Добавить баннер
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название" width="280" show-overflow-tooltip/>
                <el-table-column prop="active" label="Опубликован" width="180" >
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column prop="count" label="Кол-во элементов" width="120"/>
                <el-table-column prop="template" label="Шаблон" width="280" />
                <el-table-column prop="caption" label="Заголовок" width="280" show-overflow-tooltip/>
                <el-table-column prop="description" label="Описание" width="280" show-overflow-tooltip/>

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

        <DeleteEntityModal name_entity="Баннер"/>


        <el-dialog v-model="dialogCreate" title="Контакт" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Шаблон" label-position="top" class="mt-3">
                    <el-select v-model="form.template" >
                        <el-option v-for="item in templates" :value="item.value" :label="item.label" />
                    </el-select>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveBanner">Сохранить</el-button>
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
    banners: Array,
    templates: Array,
    title: {
        type: String,
        default: 'Сайт. Баннеры',
    },
})

const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.banners])


const form = reactive({
    name: null,
    template: null,
})

function onOpenDialog() {
    form.name = null
    form.template = null

    dialogCreate.value = true
}

function saveBanner() {
    router.post(route('admin.page.banner.store' ), form)
}

function routeClick(row) {

   router.get(route('admin.page.banner.show', {banner: row.id}))
}

function onToggle(row) {
    router.visit(route('admin.page.banner.toggle', {banner: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.banner.destroy', {banner: row.id}));
}
</script>
