<template>
    <el-dropdown class="ml-3">
        <el-button type="info" >
            <i class="fa-light fa-file-code mr-2"></i> Связанные документы
            <el-icon class="el-icon--right">
                <arrow-down/>
            </el-icon>
        </el-button>
        <template #dropdown class="m-2 accounting-based">
            <div style="width: 350px;">
                <Document :document="$order_related" />
            </div>
        </template>
    </el-dropdown>

</template>

<script lang="ts" setup>
import {inject, ref} from "vue";
import {Link} from "@inertiajs/vue3";
import Document from "./Document.vue";

interface Tree {
    url: string
    label: string
    children?: Tree[]
}
const dataSource = ref<Tree[]>([])
const $order_related = inject('$order_related')
console.log($order_related)
/*
const based = $accounting.based
const founded = $accounting.founded
if (based) dataSource.value = [...based]
*/
</script>
<style lang="scss">
.accounting-based {
    a {
        font-size: 13px !important;
    }
}
</style>
