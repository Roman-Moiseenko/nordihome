<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Справочник. Дополнительные услуги</h1>
        <div class="flex">

            <el-button type="primary" class="p-4 my-3" @click="createDialog">
                Добавить
            </el-button>

        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="editDialog"
            >
                <el-table-column prop="name" label="Название" width="300"/>
                <el-table-column prop="base" label="Базовое значение" width="160"/>
                <el-table-column prop="type_name" label="Тип услуги" width="160"/>
                <el-table-column prop="manual" label="Ручной расчет">
                    <template #default="scope">
                        <Active :active="scope.row.manual"/>
                    </template>
                </el-table-column>
                <el-table-column prop="class_name" label="Класс расчета" show-overflow-tooltip/>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.completed"
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

        <el-dialog v-model="dialogCreate" :title="form.id ? 'Изменить' : 'Новая услуга'" width="500">

            <template #footer>
                <el-form label-width="auto">
                    <el-form-item label="Услуга">
                        <el-input v-model="form.name"/>
                    </el-form-item>
                    <el-form-item label="Тип">
                        <el-select v-model="form.type">
                            <el-option v-for="item in types" :key="item.value" :value="item.value" :label="item.label"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Базовое значение">
                        <el-input v-model="form.base"/>
                    </el-form-item>
                    <el-form-item label="Ручное заполнение">
                        <el-checkbox v-model="form.manual" :checked="form.manual"/>
                    </el-form-item>
                    <el-form-item label="Класс авто расчета">
                        <el-select v-model="form.class">
                            <el-option v-for="item in classes" :key="item.value" :value="item.value" :label="item.label"/>
                        </el-select>
                    </el-form-item>
                </el-form>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="handleSave">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>
    <DeleteEntityModal name_entity="Услугу"/>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";

import {defineProps, inject, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    additions: Array,
    title: {
        type: String,
        default: 'Дополнительные услуги',
    },
    types: Array,
    classes: Array,
})
const $delete_entity = inject("$delete_entity")
const dialogCreate = ref(false)
const tableData = [...props.additions]
const form = reactive({
    id: null,
    name: null,
    base: null,
    manual: false,
    class: null,
})

function createDialog(row) {
    form.id = null
    form.name = null
    form.base = null
    form.type = null
    form.manual = false
    form.class = null
    dialogCreate.value = true
}

function editDialog(row) {
    form.id = row.id
    form.name = row.name
    form.base = row.base
    form.type = row.type
    form.manual = row.manual
    form.class = row.class
    dialogCreate.value = true
}

function handleSave() {
    let _route, _method;
    if (form.id === null) {
        _route = route('admin.guide.addition.store')
        _method = "post"
    } else {
        _route = route('admin.guide.addition.update', {addition: form.id})
        _method = "put"
    }
    router.visit(_route, {
        method: _method,
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogCreate.value = false
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.guide.addition.destroy', {addition: row.id}));
}
</script>
