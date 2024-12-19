<template>
    <el-dropdown v-if="$printed" class="ml-3">
        <el-button type="warning" plain>
            <i class="fa-light fa-print"></i>
        </el-button>
        <template #dropdown class="m-2 accounting-print">
            <div class="m-2 accounting-print">
                <div v-for="item in $printed" class="mt-1">
                    <el-tag class="cursor-pointer" type="warning" @click="getReport(item.value)">
                        {{item.label }}
                    </el-tag>
                </div>
            </div>
        </template>
    </el-dropdown>
</template>

<script setup>
import {inject, defineProps} from 'vue'
import {Link, router} from "@inertiajs/vue3";

const props = defineProps({})
//Список для печати/генерации
//{label, url}
const $printed = inject('$printed', [])
const $accounting = inject('$accounting')
console.log('printed', $printed)

function getReport(val) {
    router.post(route('admin.accounting.report', {class: val, id: $accounting.id}))
}

</script>

<style lang="scss">
.accounting-print {
    a {
        font-size: 13px !important;
    }
}
</style>
