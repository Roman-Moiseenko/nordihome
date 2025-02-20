<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категории парсера</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить вручную категорию
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Название"/>
                <el-input v-model="form.url" class="mt-1" placeholder="Ссылка (без домена)"/>
                <el-select v-model="form.brand_id" placeholder="Бренд" class="mt-1" >
                    <el-option v-for="item in brands" :value="item.id" :label="item.name" />
                </el-select>
                <el-select v-model="form.parent_id" placeholder="Родительская категория" class="mt-1" filterable clearable>
                    <el-option v-for="item in categories" :value="item.id" :label="item.name" />
                </el-select>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <SearchAddProducts :route="route('admin.parser.product.by-list')" class="ml-3"/>
        </div>
        <CategoryChildren :categories="categories" @delete:category="handleDeleteEntity" :product_categories="product_categories"/>
        <!--CategoryRow v-for="item in categories" :category="item" @delete:category="handleDeleteEntity" /-->
        <DeleteEntityModal name_entity="Категорию" />
    </el-config-provider>
</template>

<script setup lang="ts">

import {Head, router} from "@inertiajs/vue3";
import {inject, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import CategoryRow from "./Row.vue";
import CategoryChildren from "./Children.vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";

const props = defineProps({
    categories: Object,
    title: {
        type: String,
        default: 'Категории парсера',
    },
    brands: Array,
    product_categories: Array,
})

const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
console.log(props.categories)

const form = reactive({
    name: null,
    parent_id: null,
    brand_id: null,
})
function createButton() {
    router.post(route('admin.parser.category.store', form))
}

function handleDeleteEntity(id) {
    $delete_entity.show(route('admin.parser.category.destroy', {category: id}));
}
function routeClick(row) {
    router.get(route('admin.parser.category.show', {category: row.id}))
}
</script>
