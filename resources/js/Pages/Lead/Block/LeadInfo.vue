<template>
    <el-collapse :name="item.id">
        <el-collapse-item title="Consistency" name="1">
            <template #title>
                <el-tag type="danger" effect="plain">{{ func.datetime(item.created_at) }}</el-tag>
                <el-tag type="warning">{{ item.type }}</el-tag>
            </template>
            <div v-for="field in item.data">
                <template v-if="field.slug != 'id'">
                    <el-tag type="info" effect="dark">{{ field.name }}</el-tag>
                    <el-tag type="info">{{ field.value }}</el-tag>
                </template>
            </div>
        </el-collapse-item>
    </el-collapse>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import {func} from "@Res/func.js"

const props = defineProps({
    item: Object,
})
</script>
