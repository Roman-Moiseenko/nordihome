<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Справочник. Единицы измерения</h1>
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
                <el-table-column prop="code" label="Код по ОКЕИ" width="300"/>
                <el-table-column prop="manual" label="Дробление товара">
                    <template #default="scope">
                        <Active :active="scope.row.fractional"/>
                        <span v-if="scope.row.fractional" class="ml-2">({{ scope.row.fractional_name }})</span>
                    </template>
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

        <el-dialog v-model="dialogCreate" :title="form.id ? 'Изменить' : 'Новая единица измерения'" width="300">
            <template #footer>
                <el-form label-width="auto">
                    <el-form-item label="Название">
                        <el-input v-model="form.name"/>
                    </el-form-item>
                    <el-form-item label="Код по ОКЕИ">
                        <el-input v-model="form.code"/>
                    </el-form-item>
                    <el-form-item label="Дробление товара">
                        <el-checkbox v-model="form.fractional" :checked="form.fractional"/>
                    </el-form-item>
                    <el-form-item v-if="form.fractional" label="Название дробной единицы">
                        <el-input v-model="form.fractional_name"/>
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

const props = defineProps({
    measurings: Array,
    title: {
        type: String,
        default: 'Единицы измерения',
    },
})

const $delete_entity = inject("$delete_entity")
const dialogCreate = ref(false)
const tableData = [...props.measurings]
const form = reactive({
    id: null,
    name: null,
    code: null,
    fractional: false,
    fractional_name: null,
})

function createDialog() {
    form.id = null
    form.name = null
    form.code = null
    form.fractional = false
    form.fractional_name = null
    dialogCreate.value = true
}

function editDialog(row) {
    console.log(row)
    form.id = row.id
    form.name = row.name
    form.code = row.code
    form.fractional = (row.fractional === 1)
    form.fractional_name = row.fractional_name
    dialogCreate.value = true
}

function handleSave() {
    let _route, _method;
    if (form.id === null) {
        _route = route('admin.guide.measuring.store')
        _method = "post"
    } else {
        _route = route('admin.guide.measuring.update', {measuring: form.id})
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
    $delete_entity.show(route('admin.guide.measuring.destroy', {measuring: row.id}));
}
</script>
