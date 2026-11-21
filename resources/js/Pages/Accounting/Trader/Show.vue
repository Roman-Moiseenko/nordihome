<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Продавец {{ trader.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <TraderInfo :trader="trader" />
        </div>
    </el-config-provider>
</template>

<script lang="ts" setup>
import {defineProps} from "vue";
import {Head} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import TraderInfo from "./Blocks/Info.vue";

const props = defineProps({
    trader: Object,
    title: {
        type: String,
        default: 'Карточка Продавца',
    },
})

</script>
