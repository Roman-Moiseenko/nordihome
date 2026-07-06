<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-tree"></i>
                <span> Подкатегории</span>
            </span>
        </template>

        <CategoryChildren :category="category"  />
        <!--CategoryRow v-for="item in category.children" :category="item" @delete:category="handleDeleteEntity" /-->
    </el-tab-pane>
    <DeleteEntityModal name_entity="Категорию" name="category"/>
</template>
<script setup lang="ts">
//import CategoryRow from "@Page/Product/Category/CategoryRow.vue";
import {inject, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import CategoryChildren from "./../Children.vue";

const props = defineProps({
    category: Object,
})



</script>
