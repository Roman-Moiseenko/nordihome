<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl flex items-center">
            Модификация {{ modification.name }}
            <el-button circle size="small" class="ml-2" @click="openRename">
                <i class="fa-light fa-pencil"></i>
            </el-button>
        </h1>

        <div class="mt-4 p-5 bg-white rounded-md">
            <ModificationInfo :modification="modification" />
        </div>

        <div class="mt-5 p-5 bg-white rounded-md">
            <SearchModificationProduct :modification="modification" @update:product_id="handleAddProduct" />
        </div>

        <div class="mt-5 p-5 bg-white rounded-md">
            <h2 class="font-medium text-lg mb-3">Товары</h2>
            <div v-for="product in modification.products" :key="product.id" class="items-center flex mb-1 p-2">
                <div class="w-11" style="height: 40px;">
                    <img v-if="product.image" :src="product.image" style="width: 40px; height: 40px;">
                </div>
                <div class="ml-4" style="width: 200px;">
                    {{ product.code }}
                </div>
                <div class="ml-4" style="min-width: 350px;">
                    <Link type="primary" :href="route('admin.catalog.product.edit', {id: product.productId})">{{ product.name }}</Link>
                </div>
                <div>
                    <el-tag v-for="(value, index) in product.values" :key="index" :type="getType(index)" class="ml-1">{{ value }}</el-tag>
                </div>

                <el-tag v-if="product.isPrimary" type="danger" class="ml-3">Базовый</el-tag>
                <el-button v-else type="success" size="small" class="ml-3" @click="onSetPrimary(product)">Сделать первым</el-button>

                <el-button type="danger" size="small" class="ml-3" @click="handleDeleteEntity(product)">Delete</el-button>
            </div>
        </div>

        <el-dialog v-model="dialogRename" title="Переименовать модификацию" width="400">
            <el-input v-model="form.name" placeholder="Название модификации" />
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogRename = false">Отмена</el-button>
                    <el-button type="primary" @click="onRename">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из модификации"/>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, Link, router} from "@inertiajs/vue3";
import ModificationInfo from './Block/Info.vue'
import SearchModificationProduct from "@Comp/Modification/SearchModificationProduct.vue"
import {route} from "ziggy-js";
import {inject, reactive, ref} from "vue";

const props = defineProps({
    modification: Object,
    title: {
        type: String,
        default: 'Карточка модификации',
    },
})
const $delete_entity = inject("$delete_entity")

const dialogRename = ref(false)
const form = reactive({name: ''})

function openRename() {
    form.name = props.modification.name
    dialogRename.value = true
}

function onRename() {
    router.post(route('admin.catalog.modification.rename', {id: props.modification.id}), {
        name: form.name,
    }, {
        preserveScroll: true,
    })
    dialogRename.value = false
}

function getType(index) {
    if (index === 0) return 'primary'
    if (index === 1) return 'success'
    if (index === 2) return 'warning'
    if (index === 3) return 'info'
}

function handleAddProduct(productId) {
    router.post(route('admin.catalog.modification.add-product', {id: props.modification.id}), {
        product_id: productId,
    }, {
        preserveScroll: true,
    })
}

function onSetPrimary(product) {
    router.post(route('admin.catalog.modification.set-primary', {id: props.modification.id}), {
        product_id: product.productId,
    }, {
        preserveScroll: true,
    })
}

function handleDeleteEntity(product) {
    $delete_entity.show(route('admin.catalog.modification.del-product', {
        id: props.modification.id,
        product_id: product.productId,
    }));
}
</script>
