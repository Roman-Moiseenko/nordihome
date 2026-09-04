<template>
    <Head><title>{{ title }}</title></Head>

    <el-row>
        <div class="mt-2 p-5 bg-white rounded-md">
            Фильтры
        </div>
    </el-row>

    <el-splitter>
        <NewLeads/>
        <InWorkLeads/>
        <NotDecidedLeads/>
        <AwaitingLeads/>
        <PaidLeads/>
        <AssemblyLeads/>
        <DeliveryLeads/>
        <CancelledLeads/>
    </el-splitter>
</template>

<script setup lang="ts">
import {Head} from "@inertiajs/vue3";
import NewLeads from "./Panels/NewLeads.vue";
import InWorkLeads from "./Panels/InWorkLeads.vue";
import NotDecidedLeads from "./Panels/NotDecidedLeads.vue";
import AwaitingLeads from "./Panels/AwaitingLeads.vue";
import PaidLeads from "./Panels/PaidLeads.vue";
import AssemblyLeads from "./Panels/AssemblyLeads.vue";
import DeliveryLeads from "./Panels/DeliveryLeads.vue";
import CancelledLeads from "./Panels/CancelledLeads.vue";

defineProps({
    title: {
        type: String,
        default: 'Текущие заявки',
    },
})
</script>
