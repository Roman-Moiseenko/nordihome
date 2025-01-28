<template>
    <el-table
        :data="tableOzon"
        header-cell-class-name="nordihome-header"
        style="width: 100%; cursor: pointer;"
        @row-click="routeClick"
    >

        <el-table-column label="Дата" width="120">
            <template #default="scope">
                {{ func.date(scope.row.created_at) }}
            </template>
        </el-table-column>
        <el-table-column label="Действия" align="right">
            <template #default="scope">
            </template>
        </el-table-column>
    </el-table>
</template>

<script setup lang="ts">
import {func} from '@Res/func.js'
import {defineProps, ref} from "vue";

const props = defineProps({
    ozon: Array,
    drivers: Array,
})
const tableOzon = ref([...props.ozon])

</script>
