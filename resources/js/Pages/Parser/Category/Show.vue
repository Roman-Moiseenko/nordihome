<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категория {{ category.name }}</h1>
        <div class="p-5 bg-white rounded-md" v-if="category.active">
            <el-row :gutter="10" v-if="!showEdit">
                <el-col :span="8">
                    <el-descriptions :column="1" border class="mb-5">
                        <el-descriptions-item label="Связанная категория">
                            {{ category.category_name }}
                            <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                                <i class="fa-light fa-pen-to-square"></i>
                            </el-button>
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
            </el-row>
            <el-row :gutter="10" v-if="showEdit">
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="Связанная категория">
                            <el-select v-model="category_id" >
                                <el-option v-for="item in product_categories" :key="item.id" :value="item.id" :label="item.name" />
                            </el-select>
                        </el-form-item>
                        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                            Отмена
                        </el-button>
                        <el-button type="success" size="small" @click="onSetCategory">
                            Сохранить
                        </el-button>
                    </el-form>
                </el-col>
            </el-row>
            <el-button type="primary" class="mt-3" @click="products">Спарсить товары</el-button>
        </div>
        <el-tabs>
            <PanelChildren :category="category" />
            <!--PanelAttributes :category="category" /-->
            <PanelProducts :category="category" />
        </el-tabs>
    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive, watch} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";


import PanelChildren from './Panels/Children.vue'
import PanelAttributes from './Panels/Attributes.vue'
import PanelProducts from  './Panels/Products.vue'
import {ElLoading} from "element-plus";
import axios from "axios";

const props = defineProps({
    category: Object,
    product_categories: Array,
    title: {
        type: String,
        default: 'Карточка категории',
    },
})
const showEdit = ref(false)
const category_id = ref(props.category.category_id)
function onSetCategory() {
    router.visit(
        route('admin.parser.category.set-category', {category: props.category.id}), {
            method: "post",
            data: {category_id: category_id.value},
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

function products() {

    //router.post(route('admin.parser.category.parser-products', {category: props.category.id}))
    //return;

    const loading = ElLoading.service({
        lock: false,
        text: 'Парсим категорию, процесс может быть очень долгим',
        background: 'rgba(0, 0, 0, 0.7)',
    })

    const count = ref(0);
    axios.post(route('admin.parser.category.parser-products', {category: props.category.id})).then(response => {
        watch(() => count.value, (newValues, oldValues) => {
            if (newValues === response.data.length) loading.close();
        });
        let text = 'Найдено ' + response.data.length + ' товаров.'
        loading.text.value = text
        response.data.forEach(function (product) {

         //   router.post(route('admin.parser.category.parser-product', {category: props.category.id}), {product: product});

            axios.post(route('admin.parser.category.parser-product', {category: props.category.id}), {product: product}).then(response => {
                console.log('response', response)
                count.value++;
                loading.text.value = text + ' Спарсено ' + count.value
            }).catch(resolve => {
                console.log('resolve', resolve)
            })
        })
    })

    return;

    router.visit(route('admin.parser.category.parser-products', {category: props.category.id}), {
        method: "post",
        data: {
            category_id: props.category.id,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            loading.close()
        },
        onFinish: page => {
            loading.close()
        },
    })
}

</script>

<style scoped>

</style>
