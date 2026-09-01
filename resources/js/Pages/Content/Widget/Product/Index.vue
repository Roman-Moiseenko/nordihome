<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Виджеты товаров</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="createWidget" ref="buttonRef">
                Добавить виджет
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="editWidget"
            >
                <el-table-column prop="name" label="Виджет" width="280" show-overflow-tooltip/>
                <el-table-column prop="modelable_name" label="Модель" align="center">
                    <template #default="scope">
                        {{ scope.row.modelable_name }} ({{ scope.row.modelable_key }})
                    </template>
                </el-table-column>
                <el-table-column prop="count" label="Кол-во элементов" width="180" />

                <el-table-column prop="template" label="Шаблон"  align="center" />
                <el-table-column prop="published" label="Опубликован" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-tooltip effect="dark" placement="top-start" content="Скопировать шорт-код в буфер">
                            <el-button type="primary" plain @click.stop="copyBuffer(scope.row)">
                                Buffer
                            </el-button>
                        </el-tooltip>
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

        <el-dialog v-model="dialogCreate" :title="form.id ? 'Редактировать виджет' : 'Виджет'" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder=""/>
                </el-form-item>
                <el-form-item label="Шаблон" label-position="top" class="mt-3">
                    <el-select v-model="form.template" style="width: 100%">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Тип модели" label-position="top" class="mt-3">
                    <el-radio-group v-model="form.modelable" @change="onModelTypeChange">
                        <el-radio v-for="(item, index) in contentStore.types" :key="index" :value="index">
                            {{ item }}
                        </el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="Модель" label-position="top" class="mt-3">
                    <el-select v-model="form.modelable_id" clearable filterable placeholder="Выберите модель" style="width: 100%">
                        <el-option
                            v-for="item in modelSelectOptions"
                            :key="item.id"
                            :value="item.id"
                            :label="item.name"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="Заголовок" label-position="top" class="mt-3">
                    <el-input v-model="form.caption" placeholder=""/>
                </el-form-item>
                <el-form-item label="Описание" label-position="top" class="mt-3">
                    <el-input v-model="form.description" type="textarea" :rows="3"/>
                </el-form-item>
                <el-form-item label="Текст кнопки" label-position="top" class="mt-3">
                    <el-input v-model="form.button_name" placeholder=""/>
                </el-form-item>
                <el-form-item label="Ссылка (url)" label-position="top" class="mt-3">
                    <el-input v-model="form.url" placeholder=""/>
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
import {computed, defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";
import {useCatalogStore} from "@Res/catalogStore";
import {useContentStore} from "@Res/contentStore";

const props = defineProps({
    widgets: Array,
    title: {
        type: String,
        default: 'Сайт. Виджеты товаров',
    },
 //   models: Object,
    templates: Array,
})
const catalogStore = useCatalogStore()
const contentStore = useContentStore()
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.widgets])
const form = reactive({
    id: null,
    name: null,
    template: null,
    modelable_id: null,
    modelable: null,
    caption: null,
    description: null,
    button_name: null,
    url: null,
})



const selectedModelKey = computed(() => {
    const type = form.modelable
    return type && Object.prototype.hasOwnProperty.call(contentStore.types ?? {}, type) ? type : null
})
const modelSelectOptions = computed(() => {
    switch (selectedModelKey.value) {
        case 'category': return catalogStore.categories
        case 'room': return catalogStore.rooms
        case 'group': return catalogStore.groups
        case 'promotion': return catalogStore.promotions
        case 'series': return catalogStore.series
        default: return []
    }
})

function resetForm() {
    form.id = null
    form.name = null
    form.template = null
    form.modelable_id = null
    form.modelable = null
    form.caption = null
    form.description = null
    form.button_name = null
    form.url = null
}

function createWidget() {
    resetForm()
    dialogCreate.value = true
}

function editWidget(row) {
    console.log(row)
    form.id = row.id
    form.name = row.name
    form.template = row.template
    form.modelable_id = row.modelable_id
    form.modelable = row.modelable
    form.caption = row.caption
    form.description = row.description
    form.button_name = row.button_name
    form.url = row.url
    dialogCreate.value = true
}

function onModelTypeChange() {
    form.modelable_id = null
}

function copyBuffer(row) {
    navigator.clipboard.writeText('[product="' + row.id + '" name="' + row.name + '"]');
}
function saveWidget() {
    if (form.id) {
        router.visit(route('admin.content.widget.product.set-widget', {widget: form.id}), {
            method: "post",
            data: form,
            preserveScroll: true,
            preserveState: false,
            onSuccess: page => {
                dialogCreate.value = false
            },
        })
    } else {
        router.visit(route('admin.content.widget.product.store'), {
            method: "post",
            data: form,
            preserveScroll: true,
            preserveState: false,
            onSuccess: page => {
                dialogCreate.value = false
            },
        })
    }
}
function onToggle(row) {
    router.visit(route('admin.content.widget.product.toggle', {widget: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.widget.product.destroy', {widget: row.id}));
}
</script>
