<template>
    <Head><title>Категории Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категории Авито</h1>

        <div class="flex my-3">
            <el-button type="primary" :loading="syncingCategories" @click="syncCategories">
                Синхронизировать категории
            </el-button>
            <el-button type="primary" class="ml-3" :loading="syncingFields" @click="syncFields">
                Синхронизировать параметры
            </el-button>
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-tree
                :data="categories"
                node-key="id"
                :props="{ label: 'name', children: 'children' }"
                default-expand-all
                :expand-on-click-node="false"
            >
                <template #default="{ data }">
                    <span class="flex items-center">
                        <span>{{ data.name }}</span>
                        <span
                            v-if="!data.children || data.children.length === 0"
                            class="ml-1 text-gray-400"
                        >
                            ({{ data.fieldCount }})
                        </span>
                    </span>
                </template>
            </el-tree>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { route } from "ziggy-js";

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const syncingCategories = ref(false)
const syncingFields = ref(false)

function syncCategories() {
    syncingCategories.value = true
    router.visit(route('admin.marketplace.avito.sync-categories'), {
        method: 'post',
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            syncingCategories.value = false
        },
    })
}

function syncFields() {
    syncingFields.value = true
    router.visit(route('admin.marketplace.avito.sync-fields'), {
        method: 'post',
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            syncingFields.value = false
        },
    })
}
</script>
