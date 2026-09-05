<template>
    <Head><title>{{ title }}</title></Head>

    <el-row>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-checkbox-group v-model="panelsView" size="large">
                <el-checkbox-button key="new" value="new" label="Новые"  />
                <el-checkbox-button key="in_work" value="in_work" label="В работе" />
                <el-checkbox-button key="not_decided" value="not_decided" label="Клиент думает" />
                <el-checkbox-button key="awaiting" value="awaiting" label="Выставлен счет" />
                <el-checkbox-button key="paid" value="paid" label="Оплачен" />
                <el-checkbox-button key="assembly" value="assembly" label="На сборке" />
                <el-checkbox-button key="delivery" value="delivery" label="На доставке" />
                <el-checkbox-button key="cancelled" value="cancelled" label="Отменен" />
            </el-checkbox-group>
        </div>
    </el-row>

    <el-splitter style="width: 2000px;">
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
import {ref} from "vue";

const panelsView = ref(['new','in_work','not_decided', 'awaiting','paid', 'assembly', 'cancelled', 'delivery']);
defineProps({
    title: {
        type: String,
        default: 'Текущие заявки',
    },
})
</script>
