<template>
    <div :class="'p-1 ' + (lead.finishedAt !== null ? 'bg-red-600' : 'bg-white')" >
        <el-tag type="info">#{{ lead.id }}</el-tag>
        <el-tag type="danger" effect="plain">{{ lead.createdAt }}</el-tag>
        <el-tag type="warning">{{ lead.type }}</el-tag>
    </div>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

const props = defineProps({
    lead: Object,
})
</script>
<style scoped>

</style>
