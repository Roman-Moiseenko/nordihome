<template>
    <el-splitter-panel
        @dragover.prevent="onDropOver"
        @drop="onDropList"
        class="shadow-sm bg-orange-100"
    >
        <el-tag effect="dark" type="warning" size="large">Клиент думает</el-tag>
        <template v-for="lead in leads">
            <LeadInfo :lead="lead"
                      :draggable="canTransfer"
                      @dragstart="onDragStart(lead)"
                      @create:client="onDialogClient" @create:order="onDialogOrder" @add:item="onDialogItem"/>
        </template>

        <CreateClient ref="dialogCreateClient"/>
        <CreateOrder ref="dialogCreateOrder"/>
        <AddItem ref="dialogAddItem"/>
    </el-splitter-panel>
</template>

<script setup lang="ts">
import LeadInfo from "@Page/Lead/Info/Lead.vue";
import CreateClient from "@Page/Lead/Dialogs/CreateClient.vue";
import CreateOrder from "@Page/Lead/Dialogs/CreateOrder.vue";
import AddItem from "@Page/Lead/Dialogs/AddItem.vue";
import {useLeadPanel} from "./useLeadPanel";

const {
    leads, dialogCreateClient, dialogCreateOrder, dialogAddItem,
    onDialogClient, onDialogOrder, onDialogItem,
    onDragStart, onDropOver, onDropList, canTransfer,
} = useLeadPanel('not_decided');
</script>
