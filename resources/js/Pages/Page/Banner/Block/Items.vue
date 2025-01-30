<template>
    <el-table :data="[...items]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;">
        <el-table-column prop="image_file" label="IMG" width="80">
            <template #default="scope">
                <img :src="scope.row.image_file" style="height: 40px;"/>
            </template>
        </el-table-column>

        <el-table-column prop="url" label="Ссылка на страницу" width="200">
            <template #default="scope">
                <EditField :field="scope.row.url" @update:field="val => setUrl(scope.row, val)" />
            </template>
        </el-table-column>
        <el-table-column prop="caption" label="Заголовок" width="220">
            <template #default="scope">
                <EditField :field="scope.row.caption" @update:field="val => setCaption(scope.row, val)" />
            </template>
        </el-table-column>
        <el-table-column prop="description" label="Описание" >
            <template #default="scope">
                <EditField :field="scope.row.description" @update:field="val => setDescription(scope.row, val)" />
            </template>
        </el-table-column>
        <el-table-column label="Действия" align="right" width="200">

            <template #default="scope">
                <el-button size="small" type="primary" dark @click="onUp(scope.row)">
                    <i class="fa-light fa-chevron-up"></i>
                </el-button>
                <el-button size="small" type="primary" dark @click="onDown(scope.row)">
                    <i class="fa-light fa-chevron-down"></i>
                </el-button>
                <el-button size="small" type="danger" @click="handleDeleteEntity(scope.row)" plain>
                    <el-icon>
                        <Delete/>
                    </el-icon>
                </el-button>
            </template>
        </el-table-column>

    </el-table>
    <DeleteEntityModal name_entity="Элемент из баннера" />

</template>

<script setup lang="ts">

import EditField from "@Comp/Elements/EditField.vue";
import {inject, reactive} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
})
const form = reactive({
    url: null,
    caption: null,
    description: null,
})
const $delete_entity = inject("$delete_entity")

function onUp(row) {
    router.visit(route('admin.page.banner.up-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}

function onDown(row) {
    router.visit(route('admin.page.banner.down-item', {item: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}
function setUrl(row, val) {
    form.url = val
    form.caption = row.caption
    form.description = row.description
    setItem(row)
}
function setCaption(row, val) {
    form.caption = val
    form.url = row.url
    form.description = row.description

    setItem(row)
}
function setDescription(row, val) {
    form.description = val
    form.url = row.url
    form.caption = row.caption
    setItem(row)
}

function setItem(row) {
    router.visit(route('admin.page.banner.set-item', {item: row.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
        }
    })
}
function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.page.banner.del-item', {item: row.id}));
}
</script>
