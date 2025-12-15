<template>
    <div :class="'p-1 ' + (finished ? 'bg-red-600' : 'bg-white')" >
        <el-tag type="info">#{{ lead.id }}</el-tag>
        <el-tag type="danger" effect="plain">{{ func.shortdate(lead.created_at) }}</el-tag>
        <el-tag type="warning">{{ lead.type }}</el-tag>
    </div>
</template>

<script setup lang="ts">
import {func} from "@Res/func.js"
import {computed, defineProps} from "vue";

const props = defineProps({
    lead: Object,
})
const finished = computed( () => props.lead.finished_at == null ? false : ( new Date(props.lead.finished_at) < new Date() ))

</script>
<style scoped>

</style>
