<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Сайт. Кеш страниц</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onClear">
                Очистить все
            </el-button>
            <el-button type="primary" class="p-4 my-3" @click="onCreate">
                Пересоздать весь кеш
            </el-button>

            <el-button type="primary" class="p-4 my-3" @click="onCategories">
                Кеш категорий
            </el-button>
            <el-button type="primary" class="p-4 my-3" @click="onProducts">
                Кеш товаров
            </el-button>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
            >
                <el-table-column prop="name" label="Код кеша" width="200" show-overflow-tooltip/>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.active"
                                   size="small"
                                   type="danger"
                                   @click.stop="onRemove(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";

import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";

import {route} from "ziggy-js";
import axios from "axios";

const props = defineProps({
    caches: Array,
    title: {
        type: String,
        default: 'Сайт. Кеш страниц',
    },
})

const tableData = ref([...props.caches])


function onClear() {
    router.visit(route('admin.page.cache.clear'), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function onCreate() {
    router.visit(route('admin.page.cache.create'), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function onCategories() {
    router.visit(route('admin.page.cache.categories'), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onProducts() {
    router.visit(route('admin.page.cache.products'), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onRemove(row) {
    router.visit(route('admin.page.cache.remove'), {
        method: "post",
        data: {name: row.name,},
        preserveScroll: true,
        preserveState: false,
    })
}
</script>
