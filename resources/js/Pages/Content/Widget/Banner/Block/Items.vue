<template>
    <el-table :data="[...items]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;"
    >
        <el-table-column prop="image_file" label="IMG" width="80" align="left">
            <template #default="scope">
                <PhotoDTO model-type="content.banner-widget-item" :entity-id="scope.row.id" type="image" :mini="true"/>
            </template>
        </el-table-column>
        <el-table-column prop="url" label="Ссылка на страницу" width="200" align="center">

        </el-table-column>
        <el-table-column prop="slug" label="Slug" width="100" align="center">

        </el-table-column>
        <el-table-column prop="button" label="Кнопка" width="160" align="center">
        </el-table-column>
        <el-table-column prop="caption" label="Заголовок" width="240" align="center">

        </el-table-column>
        <el-table-column prop="marking" label="Маркировка" align="center">

        </el-table-column>
        <el-table-column prop="description" label="Описание" align="center" width="300" show-overflow-tooltip>

        </el-table-column>
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
            <el-form-item label="Slug" class="mt-3">
                <el-input v-model="form.slug"/>
            </el-form-item>
            <el-form-item label="Кнопка" class="mt-3">
                <el-input v-model="form.button"/>
            </el-form-item>
            <el-form-item label="Ссылка" class="mt-3">
                <el-input v-model="form.url"/>
            </el-form-item>
            <el-form-item label="Заголовок" class="mt-3">
                <el-input v-model="form.caption"/>
            </el-form-item>
            <el-form-item label="Описание" class="mt-3">
                <el-input v-model="form.description" type="textarea" :rows="5"/>
            </el-form-item>
            <el-form-item label="Маркировка" class="mt-3">
                <el-input v-model="form.marking"/>
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
    slug: null,
    button: null,
    url: null,
    caption: null,
    description: null,
    marking: null,
})
const $delete_entity = inject("$delete_entity")
const showDialog = ref(false)

function onUp(row) {
    router.visit(route('admin.content.widget.banner.up-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onDown(row) {
    router.visit(route('admin.content.widget.banner.down-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function handleEdit(row) {
    form.id = row.id
    form.slug = row.slug
    form.button = row.button
    form.url = row.url
    form.caption = row.caption
    form.description = row.description
    showDialog.value = true
}

function getRow(row) {
    form.slug = row.slug
    form.button = row.button
    form.url = row.url
    form.caption = row.caption
    form.description = row.description
}

function setSlug(row, val) {
    getRow(row)
    form.slug = val
    setItem(row)
}

function setButton(row, val) {
    getRow(row)
    form.button = val
    setItem(row)
}

function setUrl(row, val) {
    getRow(row)
    form.url = val
    setItem(row)
}

function setCaption(row, val) {
    getRow(row)
    form.caption = val
    setItem(row)
}

function setMarking(row, val) {
    getRow(row)
    form.marking = val
    setItem(row)
}

function setDescription(row, val) {
    getRow(row)
    form.description = val
    setItem(row)
}

function setItem() {
    router.visit(route('admin.content.widget.banner.set-item', {item: form.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.widget.banner.del-item', {item: row.id}));
}
</script>
