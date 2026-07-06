<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категория {{ category.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <el-row :gutter="10">
                <el-col :span="12">
                    <el-descriptions :column="2" border class="mb-5">
                        <el-descriptions-item label="Id Категории в Ikea">
                            {{ category.ikeaId}}
                        </el-descriptions-item>
                        <el-descriptions-item label="Доступна для парсинга">
                            <Active :active="category.active" />
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
            </el-row>

            <el-button  v-if="category.active" type="primary" class="mt-3" @click="products">Спарсить товары</el-button>
        </div>
        <el-tabs>
            <PanelChildren :category="category" />
            <PanelProducts :category-id="category.id" />
        </el-tabs>
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive, watch} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import PanelChildren from './Panels/Children.vue'
import PanelProducts from  './Panels/Products.vue'
import {useCatalogStore} from "@Res/catalogStore.ts";
import Active from "@Comp/Elements/Active.vue";
import api from "@Res/api";

const useCatalog = useCatalogStore()

const props = defineProps({
    category: Object,
    title: {
        type: String,
        default: 'Карточка категории',
    },
})

function products() {
    api.post(route('admin.parser.category.parser-products', {category_parser: props.category.id}))
}

</script>

<style scoped>

</style>
