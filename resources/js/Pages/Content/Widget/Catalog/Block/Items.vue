<template>
    <el-table :data="[...items]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;"
    >
        <el-table-column prop="image" label="IMG" width="40" align="center">
            <template #default="scope">
                <!-- тут изображение -->
            </template>
        </el-table-column>
        <el-table-column prop="name" label="Название" align="center"/>
        <el-table-column prop="model_type" label="Сущность" width="200" align="center"/>

        <el-table-column prop="caption" label="Заголовок" width="200" align="center" />
        <el-table-column prop="description" label="Описание" align="center" width="300" show-overflow-tooltip />

        <el-table-column label="Действия" align="right" width="200">
            <template #default="scope">
                <el-button size="small" type="primary" dark @click="onUp(scope.row)">
                    <i class="fa-light fa-chevron-up"></i>
                </el-button>
                <el-button size="small" type="primary" dark @click="onDown(scope.row)">
                    <i class="fa-light fa-chevron-down"></i>
                </el-button>
                <el-button size="small" type="success" dark @click="handleEdit(scope.row)">
                    <i class="fa-light fa-pencil"></i>
                </el-button>
                <el-button size="small" type="danger" @click="handleDeleteEntity(scope.row)" plain>
                    <el-icon>
                        <Delete/>
                    </el-icon>
                </el-button>
            </template>
        </el-table-column>

    </el-table>

    <el-dialog v-model="showDialog" title="Редактировать">
        <el-form label-width="auto">



            <el-form-item label="Заголовок" class="mt-3">
                <el-input v-model="form.caption"/>
            </el-form-item>
            <el-form-item label="Описание" class="mt-3">
                <el-input v-model="form.description" type="textarea" :rows="5"/>
            </el-form-item>
        </el-form>

        <template #footer>
            <div class="dialog-footer">
                <el-button @click="showDialog = false">Отмена</el-button>
                <el-button type="primary" @click="setItem">Сохранить</el-button>
            </div>
        </template>
    </el-dialog>
    <DeleteEntityModal name_entity="Элемент из баннера"/>

</template>

<script setup lang="ts">

import EditField from "@Comp/Elements/EditField.vue";
import {inject, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    items: Array,
})
const form = reactive({
    id: null,
    modelId: null,
    modelType: null,
    caption: null,
    description: null,
})
const $delete_entity = inject("$delete_entity")
const showDialog = ref(false)

function onUp(row) {
    router.visit(route('admin.content.widget.catalog.up-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onDown(row) {
    router.visit(route('admin.content.widget.catalog.down-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function handleEdit(row) {
    form.id = row.id
    form.modelId = row.model_id
    form.modelType = row.model_type
    form.caption = row.caption
    form.description = row.description
    showDialog.value = true
}

function setItem() {
    router.visit(route('admin.content.widget.catalog.set-item', {item: form.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.widget.catalog.del-item', {item: row.id}));
}
</script>
