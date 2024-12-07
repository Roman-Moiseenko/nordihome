<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категории товаров</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать категорию
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Название"/>
                <el-select v-model="form.parent_id" placeholder="Родительская категория" class="mt-1" filterable clearable>
                    <el-option v-for="item in categories" :value="item.id" :label="item.name" />
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>
        <CategoryRow v-for="item in categories" :category="item" @delete:category="handleDeleteEntity" />

        <DeleteEntityModal name_entity="Категорию" />
    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import CategoryRow from "@Page/Product/Category/CategoryRow.vue";

const props = defineProps({
    categories: Object,
    title: {
        type: String,
        default: 'Категории товаров',
    },
})

const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
console.log(props.categories)

const form = reactive({
    name: null,
    parent_id: null,
})
function createButton() {
    router.post(route('admin.product.category.store', form))
}

function handleDeleteEntity(id) {
    $delete_entity.show(route('admin.product.category.destroy', {category: id}));
}
function routeClick(row) {
    router.get(route('admin.product.category.show', {category: row.id}))
}
</script>
