<template>
    <el-collapse v-if="lead.leads.length > 0" :name="'leads' + lead.id">
        <el-collapse-item title="Повторное обращение" name="1">
            <div v-for="_lead in lead.leads">
                <template v-if="_lead.id !== lead.id">
                    <el-tag type="info" effect="dark">{{ func.date(_lead.created_at) }}</el-tag>
                    <el-tag type="success" effect="dark">{{ _lead.status }}</el-tag>
                </template>
            </div>
        </el-collapse-item>
    </el-collapse>
</template>

<script setup lang="ts">
import {func} from "@Res/func.js"
const props = defineProps({
    lead: Object,
})
</script>

<style scoped>

</style>
