<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Фиды для рынков</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="dialogCreate = true" ref="buttonRef">
                Создать фид
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="classes.TableActive"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Фид" width="280" show-overflow-tooltip/>


                <el-table-column prop="active" label="Доступен" width="130" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <template v-if="scope.row.active">
                        <el-tooltip effect="dark" placement="top-start" content="Скопировать ссылку в буфер">
                            <el-button type="primary" plain @click.stop="copyBufferGoogle(scope.row)">
                                Google
                            </el-button>
                        </el-tooltip>
                        <el-tooltip effect="dark" placement="top-start" content="Скопировать ссылку в буфер">
                            <el-button type="primary" plain @click.stop="copyBufferYandex(scope.row)">
                                Yandex
                            </el-button>
                        </el-tooltip>
                        </template>
                        <el-button size="small"
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

        <DeleteEntityModal name_entity="Фид"/>

        <el-dialog v-model="dialogCreate" title="Фид" width="500">
            <el-form label-width="auto">
                <el-form-item label="Имя фида" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Внутреннее"/>
                    <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
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
import {classes} from '@Res/className.ts'

import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";

const props = defineProps({
    feeds: Array,
    errors: Object,
    title: {
        type: String,
        default: 'Фиды для рынков',
    },
})

const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.feeds])
const form = reactive({
    name: null,
})

function savePage() {
    dialogCreate.value = false;
    router.visit(route('admin.unload.feed.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {

        }
    })
}
function onToggle(row) {
    router.visit(route('admin.unload.feed.toggle', {feed: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function routeClick(row) {
    router.get(route('admin.unload.feed.show', {feed: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.unload.feed.destroy', {feed: row.id}));
}

function copyBufferGoogle(row) {

    navigator.clipboard.writeText(window.location.origin + '/feed/' + row.id + '/feed-google.xml');
}
function copyBufferYandex(row) {
    navigator.clipboard.writeText(window.location.origin + '/feed/' + row.id + '/feed-yandex.yml');
}
</script>
