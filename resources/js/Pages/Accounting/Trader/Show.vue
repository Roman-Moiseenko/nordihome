<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Продавец {{ trader.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <TraderInfo :trader="trader" :organizations="organizations" :vat="vat" />
        </div>
    </el-config-provider>
</template>

<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import TraderInfo from "./Blocks/Info.vue";

const props = defineProps({
    trader: Object,
    organizations: Array,
    vat: Array,
    title: {
        type: String,
        default: 'Карточка Продавца',
    },
})

</script>
