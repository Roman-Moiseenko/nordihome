<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Справочник. НДС</h1>
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
                <el-table-column prop="value" label="Значение %">
                </el-table-column>
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

        <el-dialog v-model="dialogCreate" :title="form.id ? 'Изменить' : 'Новый НДС'" width="300">
            <template #footer>
                <el-form label-width="auto">
                    <el-form-item label="Название">
                        <el-input v-model="form.name"/>
                    </el-form-item>
                    <el-form-item label="Значение">
                        <el-input v-model="form.value" :formatter="val => func.MaskCount(val, 0, 50)"/>
                    </el-form-item>
                </el-form>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="handleSave">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>
    <DeleteEntityModal name_entity="Единицу измерения"/>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import Active from "@Comp/Elements/Active.vue";

import { func } from "@Res/func.js"

const props = defineProps({
    VATs: Array,
    title: {
        type: String,
        default: 'НДС',
    },
})

const $delete_entity = inject("$delete_entity")
const dialogCreate = ref(false)
const tableData = [...props.VATs]
const form = reactive({
    id: null,
    name: null,
    value: null,
})

function createDialog() {
    form.id = null
    form.name = null
    form.value = null
    dialogCreate.value = true
}

function editDialog(row) {
    console.log(row)
    form.id = row.id
    form.name = row.name
    form.value = row.value
    dialogCreate.value = true
}

function handleSave() {
    let _route, _method;
    if (form.id === null) {
        _route = route('admin.guide.vat.store')
        _method = "post"
    } else {
        _route = route('admin.guide.vat.update', {vat: form.id})
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
    $delete_entity.show(route('admin.guide.vat.destroy', {vat: row.id}));
}
</script>
