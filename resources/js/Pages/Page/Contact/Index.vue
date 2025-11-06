<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Контакты</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onOpenDialog" ref="buttonRef">
                Добавить контакт
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
                <el-table-column prop="slug" label="Slug" width="120" align="center"/>
                <el-table-column prop="icon" label="Иконка" width="180">
                    <template #default="scope">
                        <i :class="scope.row.icon"/>
                    </template>
                </el-table-column>
                <el-table-column prop="icon" label="Цвет" width="180" >
                    <template #default="scope">
                        <span :style="'background:' + scope.row.color + '; padding: 0 10px;'"> </span>
                    </template>
                </el-table-column>
                <el-table-column prop="url" label="Ссылка" width="280" show-overflow-tooltip/>
                <el-table-column prop="published" label="Опубликован" width="180" align="center">
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
                        <el-button
                            size="small"
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

        <DeleteEntityModal name_entity="Контакт"/>

        <el-dialog v-model="dialogCreate" title="Контакт" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Подпись, ALT"/>
                </el-form-item>
                <el-form-item label="Slug" label-position="top" class="mt-3">
                    <el-input v-model="form.slug" placeholder="Slug"/>
                </el-form-item>
                <el-form-item label="Класс иконки" label-position="top" class="mt-3">
                    <el-input v-model="form.icon" placeholder="fontawesome 6.0"/>
                </el-form-item>

                <el-color-picker v-model="form.color" />
                <el-form-item label="Ссылка на контакт" label-position="top" class="mt-3">
                    <el-input v-model="form.url" placeholder="https://"/>
                </el-form-item>
                <el-form-item label="Тип для аналитики" label-position="top" class="mt-3">
                    <el-input-number v-model="form.type" min="0"/>
                </el-form-item>

            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveContact">Сохранить</el-button>
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
    contacts: Array,
    title: {
        type: String,
        default: 'Сайт. Контакты',
    },
})

const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.contacts])


const form = reactive({
    id: null,
    name: null,
    icon: null,
    url: null,
    type: null,
    color: null,
    slug: null,
})

function onOpenDialog() {
    form.id = null
    form.name = null
    form.icon = null
    form.url = null
    form.type = null
    form.color = null
    form.slug = null
    dialogCreate.value = true
}

function saveContact() {
    let _route = '';
    if (form.id === null) {
        _route = route('admin.page.contact.store' )
    } else {
        _route = route('admin.page.contact.set-info', {contact: form.id})
    }

    router.visit(_route , {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableData.value = [...page.props.contacts]
        }
    })


    dialogCreate.value = false
}

function routeClick(row) {
    form.id = row.id
    form.name = row.name
    form.icon = row.icon
    form.url = row.url
    form.type = row.type
    form.color = row.color
    form.slug = row.slug
    dialogCreate.value = true
}

function onToggle(row) {
    contactRouter(row.id, 'toggle')
}
function onUp(row) {
    contactRouter(row.id, 'up')
}
function onDown(row) {
    contactRouter(row.id, 'down')
}

const contactRouter = (id: Number, action: String) => {
    router.visit(route('admin.page.contact.' + action, {contact: id}) , {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
          //  console.log(page.props.contacts)
            tableData.value = [...page.props.contacts]
           // props.contacts = [...page.props.contacts]
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.contact.destroy', {contact: row.id}));
}
</script>
