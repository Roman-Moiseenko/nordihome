<template>
    <el-splitter-panel
        @dragover.prevent="onDropOver"
        @drop="onDropList"
        class="shadow-sm bg-gray-100"
        :size="PANEL_SIZE"
        :resizable="false"
    >
        <el-tag effect="dark" type="danger" size="large">Отменен</el-tag>
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
import {useLeadPanel, PANEL_SIZE} from "./useLeadPanel";

const {
    leads, dialogCreateClient, dialogCreateOrder, dialogAddItem,
    onDialogClient, onDialogOrder, onDialogItem,
    onDragStart, onDropOver, onDropList, canTransfer,
} = useLeadPanel('cancelled');
</script>
