<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Модификация {{ modification.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <ModificationInfo :modification="modification" />
        </div>
        <div class="mt-5 mb-2 p-5 bg-white rounded-md">
            <ModificationSearchProduct  :action="'show'" @update:product_id="handleGetProduct" />
        </div>

        <div v-for="product in modification.products" class="p-3 bg-white rounded-md items-center flex mb-1 p-2">
            <div class="w-11" style="height: 40px;">
                <img v-if="product.image" :src="product.image" style="width: 40px; height: 40px;">
            </div>
            <div class="ml-4" style="width: 120px;">
                {{ product.code }}
            </div>
            <div class="ml-4" style="width: 350px;">
                <Link type="primary" :href="route('admin.product.edit', {product: product.id})">{{ product.name }}</Link>
            </div>
            <div>
                <el-tag v-for="(variant, index) in product.variants" :type="getType(index)" class="ml-1">{{ variant }}</el-tag>
            </div>
            <div class="ml-4">
                <el-button type="danger" size="small" @click="handleDeleteEntity(product)">Delete</el-button>
            </div>
        </div>
    </el-config-provider>
    <DeleteEntityModal name_entity="Товар из модификации"/>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, Link, router} from "@inertiajs/vue3";
import ModificationInfo from './Block/Info.vue'
import {route} from "ziggy-js";
import ModificationSearchProduct from "@Comp/Modification/SearchProduct.vue"
import {inject} from "vue";

const props = defineProps({
    modification: Object,
    title: {
        type: String,
        default: 'Карточка модификации',
    },
})
const $delete_entity = inject("$delete_entity")

function getType(index) {
    if (index === 0) return 'primary'
    if (index === 1) return 'success'
    if (index === 2) return 'warning'
    if (index === 3) return 'info'
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.modification.del-product', {modification: props.modification.id, product_id: row.id}));
}
function handleGetProduct(val) {
    router.visit(route('admin.product.modification.add-product', {modification: props.modification.id}), {
        method: "post",
        data: {
            product_id: val
        },
        preserveScroll: true,
        preserveState: false,
    })
}
</script>
