<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl my-2">Редактирование товара {{ product.name }}</h1>
        <div class="mt-2 p-3 bg-white rounded-md">
            <el-tabs>

                <PanelCommon :product="product"
                             :errors="errors"
                             :categories="categories"
                             :brands="brands"
                             :country="country"
                             :vat="vat"
                             :measuring="measuring"
                             :markingType="markingType"
                />

                <PanelDescription :product="product"/>

            </el-tabs>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive} from "vue";

//Панели
import PanelCommon from './Panels/Common.vue'
import PanelDescription from './Panels/Description.vue'

const props = defineProps({
    product: Object,
    errors: Object,
    categories: Array,
    brands: Array,
    country: Array,
    vat: Array,
    measuring: Array,
    markingType: Array,
    distributors: Array,
    title: {
        type: String,
        default: 'Редактирование товара',
    },
})
console.log('Edit', props.product.slug)
</script>

<style scoped>

</style>
