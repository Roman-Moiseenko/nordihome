<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Справочники</h1>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="[...guides]"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >

                <el-table-column label="" width="60">
                    <template #default="scope">
                        <i :class="scope.row.font_awesome"></i>
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Справочник" />
                <el-table-column prop="comment" label="Описание" show-overflow-tooltip/>
            </el-table>
        </div>

    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {defineProps} from "vue";

const props = defineProps({
    guides: Array,
    title: {
        type: String,
        default: 'Справочники',
    },

})
function routeClick(row) {
    router.get(route(row.route))
}
</script>
