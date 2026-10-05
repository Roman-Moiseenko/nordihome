<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Группа аналогов {{ equivalent.name }}</h1>
        </div>
        <div class="p-5 bg-white rounded-md">
            <EquivalentInfo :equivalent="equivalent" />
        </div>

        <el-tabs>
            <PanelProducts :equivalent-id="equivalent.id" />
        </el-tabs>
    </el-config-provider>
</template>
<script lang="ts" setup>
import {defineProps} from "vue";
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import EquivalentInfo from  './Block/Info.vue'
import PanelProducts from "./Panels/Products.vue";

const props = defineProps({
    equivalent: Object,
    title: {
        type: String,
        default: 'Карточка группы аналогов',
    },
})

</script>
