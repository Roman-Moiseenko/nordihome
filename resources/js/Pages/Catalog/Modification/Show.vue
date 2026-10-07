<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Модификация {{ modification.name }}</h1>

        <div class="mt-4 p-5 bg-white rounded-md">
            <ModificationInfo :modification="modification" />
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
                    <Link type="primary" :href="route('admin.catalog.product.edit', {product: product.product_id})">{{ product.name }}</Link>
                </div>
                <div>
                    <el-tag v-for="(value, index) in product.values" :key="index" :type="getType(index)" class="ml-1">{{ value }}</el-tag>
                </div>
            </div>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, Link} from "@inertiajs/vue3";
import ModificationInfo from './Block/Info.vue'
import {route} from "ziggy-js";

const props = defineProps({
    modification: Object,
    title: {
        type: String,
        default: 'Карточка модификации',
    },
})

function getType(index) {
    if (index === 0) return 'primary'
    if (index === 1) return 'success'
    if (index === 2) return 'warning'
    if (index === 3) return 'info'
}
</script>
