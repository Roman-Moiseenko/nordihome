<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Поставщик {{ trader.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <TraderInfo :trader="trader" :organizations="organizations"  />
        </div>


    </el-config-provider>
</template>

<script lang="ts" setup>
import {inject, ref, defineProps, provide} from "vue";
import {Head, Link, router} from "@inertiajs/vue3";
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Pagination from '@Comp/Pagination.vue'
import TraderInfo from "./Blocks/Info.vue";

const props = defineProps({
    trader: Object,
    organizations: Array,
    title: {
        type: String,
        default: 'Карточка Продавца',
    },

})


function classCostColor(row) {
    if (!row.pre_cost) return ''
    if (row.cost > row.pre_cost) return 'text-red-600'
    if (row.cost < row.pre_cost) return 'text-green-600'
    return ''
}

</script>
