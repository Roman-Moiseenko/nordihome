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
                ref="tableRef"
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column label="" width="40" align="center">
                    <template #default>
                        <div class="drag-handle px-1" title="Перетащить">
                            <i class="fa-solid fa-grip-vertical"></i>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Страница" width="280" show-overflow-tooltip/>
                <el-table-column prop="slug" label="Slug" width="" />
                <el-table-column prop="template" label="Шаблон" width="120" show-overflow-tooltip/>
                <el-table-column prop="published" label="Опубликована" width="130" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.published" />
                    </template>
                </el-table-column>
                <el-table-column prop="oldRender" label="ContentBlock" width="130" align="center">
                    <template #default="scope">
                        <Active :active="!scope.row.oldRender" />
                    </template>
                </el-table-column>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
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
                    <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                </el-form-item>
                <el-form-item label="Ссылка" label-position="top" class="mt-3">
                    <el-input v-model="form.slug" placeholder="Заполнится автоматически" clearable/>
                    <div v-if="errors.slug" class="text-red-700">{{ errors.slug }}</div>
                </el-form-item>
                <el-form-item label="Родительская страница" label-position="top" class="mt-3">
                    <el-select v-model="form.parentId" filterable clearable>
                        <el-option v-for="item in useContent.pages" :value="item.id" :label="item.name" />
                    </el-select>
                    <div v-if="errors.parentId" class="text-red-700">{{ errors.parentId }}</div>
                </el-form-item>

                <el-form-item label="Шаблон" label-position="top" class="mt-3">
                    <el-select v-model="form.template" filterable clearable>
                        <el-option v-for="item in templates" :value="item.value" :label="item.label" />
                    </el-select>
                    <div v-if="errors.template" class="text-red-700">{{ errors.template }}</div>
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
import Sortable from "sortablejs";

import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, onBeforeUnmount, onMounted, reactive, ref} from "vue";

import {route} from "ziggy-js";
import {useContentStore} from "@Res/contentStore";

const props = defineProps({
    pages: Array,
    errors: Object,
    title: {
        type: String,
        default: 'Сайт. Страницы',
    },
    templates: Array,
})
const useContent = useContentStore()
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.pages])
const tableRef = ref()
let sortable: Sortable | null = null
const form = reactive({
    name: null,
    slug: null,
    template: null,
    parentId: null,
})

function savePage() {
    router.visit(route('admin.content.page.store'), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogCreate.value = false;
            console.log(page.props)
        }
    })
}
function onToggle(row) {
    router.visit(route('admin.content.page.toggle', {id: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function routeClick(row) {
    router.get(route('admin.content.page.show', {id: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.page.destroy', {id: row.id}));
}

onMounted(() => {
    const tbody = tableRef.value?.$el?.querySelector('tbody')
    if (!tbody) return

    sortable = Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd(evt: Sortable.SortableEvent) {
            const oldIndex = evt.oldIndex
            const newIndex = evt.newIndex

            if (oldIndex === undefined || newIndex === undefined || oldIndex === newIndex) {
                return
            }

            const moved = tableData.value[oldIndex]
            if (!moved || moved.id === undefined) {
                return
            }

            router.visit(route('admin.content.page.move', {id: moved.id}), {
                method: "post",
                data: {position: newIndex},
                preserveScroll: true,
                preserveState: false,
                onError: () => {
                    router.reload({preserveScroll: true, preserveState: false})
                },
            })
        },
    })
})

onBeforeUnmount(() => {
    if (sortable) {
        sortable.destroy()
        sortable = null
    }
})
</script>
