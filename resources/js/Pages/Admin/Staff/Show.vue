<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Карточка сотрудника</h1>
        <div class="mt-2 p-5 bg-white rounded-md">
            <BlockInfo v-if="!showEdit"
                       :staff="staff"
                       :responsibilities="responsibilities"
                       :errors="errors"
                       @update:show="val => showEdit = val"/>
            <BlockEdit v-if="showEdit"
                       :staff="staff"
                       :responsibilities="responsibilities"
                       :roles="roles"
                       :errors="errors"
                       @update:show="val => showEdit = val"/>
        </div>
    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head} from "@inertiajs/vue3";
import { ref } from 'vue'
import BlockInfo from "./Block/Info.vue"
import BlockEdit from "./Block/Edit.vue"

const props = defineProps({
    staff: Object,
    errors: Object,
    title: {
        type: String,
        default: 'Карточка сотрудника',
    },
    responsibilities: Array,
    roles: Array,
})

const showEdit = ref(false)

</script>
