<script setup lang="ts">
import LeadInfo from "@Page/Lead/Info/Lead.vue";

const leads = [];


</script>

<template>
    <el-splitter-panel
        @dragover.prevent="onDropOver(index)"
        @drop="onDropList(index)"
        class="shadow-sm bg-green-100"
    >
        <el-tag effect="dark" type="primary" size="large">Новый</el-tag>
        <template v-for="lead in leads">
            <LeadInfo :lead="lead"
                      :draggable="true" @dragstart="onDragStart(lead, index)"
                      @create:user="onDialogUser" @create:order="onDialogOrder" @add:item="onDialogItem"/>
        </template>
    </el-splitter-panel>
</template>

<style scoped>

</style>
