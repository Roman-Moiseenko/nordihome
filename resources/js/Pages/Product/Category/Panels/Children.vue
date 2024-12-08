<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-tree"></i>
                <span> Подкатегории</span>
            </span>
        </template>
        <el-popover :visible="visible_create" placement="bottom-start" :width="246">
            <template #reference>
                <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                    Добавить подкатегорию
                    <el-icon class="ml-1"><ArrowDown /></el-icon>
                </el-button>
            </template>
            <el-input v-model="form.name" placeholder="Название"/>
            <div class="mt-2">
                <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
            </div>
        </el-popover>
        <CategoryChildren :category="category" @delete:category="handleDeleteEntity" />
        <!--CategoryRow v-for="item in category.children" :category="item" @delete:category="handleDeleteEntity" /-->
    </el-tab-pane>
    <DeleteEntityModal name_entity="Категорию" />
</template>
<script setup lang="ts">
import CategoryRow from "@Page/Product/Category/CategoryRow.vue";
import {inject, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import CategoryChildren from "@Page/Product/Category/CategoryChildren.vue";

const props = defineProps({
    category: Object,
})
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const form = reactive({
    name: null,
    parent_id: props.category.id,
})
function createButton() {
    router.post(route('admin.product.category.store', form))
}
function handleDeleteEntity(id) {
    $delete_entity.show(route('admin.product.category.destroy', {category: id}));
}
</script>
