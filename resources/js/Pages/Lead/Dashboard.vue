<template>
    <Head><title>{{ title }}</title></Head>

    <el-row>
        <div class="mt-2 p-5 bg-white rounded-md">
            Фильтры
        </div>
    </el-row>

    <el-splitter>
        <template v-for="(key, index) in boards">
            <el-splitter-panel v-if="index != 'completed'"
                               @dragover.prevent="onDropOver(index)"
                               @drop="onDropList(index)"
                               :class="'shadow-sm ' + background[index]"
            >
                <el-tag effect="dark" :type="button_color[index]" size="large">{{ key }}</el-tag>
                <template v-for="lead in leads[index]">
                    <LeadInfo :lead="lead"
                              :draggable="true" @dragstart="onDragStart(lead, index)"
                              @create:client="onDialogUser" @create:order="onDialogOrder" @add:item="onDialogItem"/>
                </template>
            </el-splitter-panel>
        </template>
    </el-splitter>

    <CreateUser ref="dialogCreateUser"/>
    <CreateOrder ref="dialogCreateOrder"/>
    <AddItem ref="dialogAddItem"/>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import LeadInfo from "./Info/Lead.vue";
import CreateUser from "./Dialogs/CreateUser.vue";
import CreateOrder from "./Dialogs/CreateOrder.vue";
import AddItem from "./Dialogs/AddItem.vue";

const props = defineProps({
    leads: Array,
    title: {
        type: String,
        default: 'Текущие заявки',
    },
    boards: Object,
    staffs: Array,
})

const background = {
    new_lead: 'bg-green-100',
    in_work: 'bg-red-100',
    not_decided: 'bg-orange-100',
    awaiting: 'bg-cyan-100',
    paid: 'bg-lime-100',
    assembly: 'bg-slate-100',
    delivery: 'bg-stone-100',
}
const button_color = {
    new_lead: 'success',
    in_work: 'danger',
    not_decided: 'warning',
    awaiting: 'info',
    paid: 'info',
    assembly: 'info',
    delivery: 'info',
}
const dragItem = ref(null);
const dragFrom = ref(null);

const dialogCreateUser = ref(null)
const dialogCreateOrder = ref(null)
const dialogAddItem = ref(null)

function onDialogUser(val) {
    dialogCreateUser.value.open(val)
}

function onDialogOrder(val) {
    dialogCreateOrder.value.open(val)
}

function onDialogItem(val) {
    dialogAddItem.value.open(val)
}

//Drag & Drop
function onDropOver(b) {
     console.log('onDropOver', b)
}

function onDropList(key) {
    if (dragFrom.value === key) return;
    if (key > 3) return;
    router.visit(route('admin.lead.set-status', {lead: dragItem.value.id}), {
        method: "post",
        data: {status: key},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {

        }
    })
    dragItem.value = null
    dragFrom.value = null
}

function onDragStart(item, t) {
    dragItem.value = item
    dragFrom.value = t
}

</script>
