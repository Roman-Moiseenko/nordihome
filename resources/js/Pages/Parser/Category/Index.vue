<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категории парсера</h1>

        <CategoryChildren :categories="categories" @delete:category="handleDeleteEntity"/>
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

const props = defineProps({
    categories: Object,
    title: {
        type: String,
        default: 'Категории парсера',
    },
   // brands: Array,
})

const $delete_entity = inject("$delete_entity")

/*
const form = reactive({
    name: null,
    parent_id: null,
    brand_id: null,
})

function createButton() {
    router.post(route('admin.parser.category.store', form))
}
*/
function handleDeleteEntity(id) {
    $delete_entity.show(route('admin.parser.category.destroy', {category_parser: id}));
}
/*
function routeClick(row) {
    router.get(route('admin.parser.category.show', {category_parser: row.id}))
}*/
</script>
