<template>
    <el-table :data="[...items]"
              header-cell-class-name="nordihome-header"
              style="width: 100%;"
    >
        <el-table-column prop="image" label="IMG" width="60" align="center">
            <template #default="scope">
                <el-image
                    v-if="scope.row.image"
                    :src="scope.row.image"
                    :preview-src-list="[scope.row.image]"
                    preview-teleported
                    fit="cover"
                    style="width: 40px; height: 40px; border-radius: 4px;"
                />
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
                <el-button size="small" type="success" dark @click="emit('edit', scope.row)">
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

    <DeleteEntityModal name_entity="Элемент каталога"/>

</template>

<script setup lang="ts">

import {inject} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
})
const emit = defineEmits(['edit'])

const $delete_entity = inject("$delete_entity")

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

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.content.widget.catalog.del-item', {item: row.id}));
}
</script>
