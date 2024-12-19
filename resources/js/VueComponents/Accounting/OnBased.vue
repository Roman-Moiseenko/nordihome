<template>
    <el-dropdown class="ml-3">
        <el-button type="success" plain>
            <i class="fa-light fa-file-code mr-2"></i> Документы
            <el-icon class="el-icon--right">
                <arrow-down/>
            </el-icon>
        </el-button>
        <template #dropdown class="m-2 accounting-based">
            <div class="m-2 accounting-based">
                <div v-if="founded">
                    <el-tag>Основание</el-tag>
                    <div v-for="item in founded">
                        <Link class="ml-6" type="primary" :href="item.url">{{ item.label }}</Link>
                    </div>
                </div>
                <div v-if="based">
                    <el-tag type="success">Дочерние</el-tag>
                    <el-tree
                        :data="dataSource"
                        default-expand-all
                    >
                        <template #default="{ node, data }">
                            <Link type="success" :href="data.url">{{ data.label }}</Link>
                        </template>
                    </el-tree>
                </div>
            </div>
        </template>
    </el-dropdown>

</template>

<script lang="ts" setup>
import {inject, ref} from "vue";
import {Link} from "@inertiajs/vue3";

interface Tree {
    url: string
    label: string
    children?: Tree[]
}
const dataSource = ref<Tree[]>([])
const $accounting = inject('$accounting')
const based = $accounting.based
const founded = $accounting.founded
if (based) dataSource.value = [...based]

</script>
<style lang="scss">
.accounting-based {
    a {
        font-size: 13px !important;
    }
}
</style>
