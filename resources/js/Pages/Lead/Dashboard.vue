<template>
    <Head><title>{{ title }}</title></Head>

    <el-splitter>

        <el-splitter-panel
            @dragover.prevent="onDropOver(1)"
            @drop="onDropList(1)"
            class="shadow-sm bg-green-100"
        >
            <el-tag effect="dark" type="success" size="large">НОВЫЕ</el-tag>
            <template v-for="item in new_leads">
                <div class="shadow-lg bg-white mb-1" style="width: 240px;" draggable="true"
                     @dragstart="onDragStart(item, 1)">
                    <LeadInfo :item="item" />
                </div>
            </template>

        </el-splitter-panel>
        <el-splitter-panel
            @dragover.prevent="onDropOver(2)"
            @drop="onDropList(2)"
            class="shadow-sm bg-red-100"
        >
            <el-tag effect="dark" type="danger" size="large">МОИ В РАБОТЕ</el-tag>
            <template v-for="item in my_leads.in_work">
                <div class="shadow-lg bg-white mb-1" style="width: 240px;" draggable="true"
                     @dragstart="onDragStart(item, 2)">
                    <LeadInfo :item="item" />
                </div>
            </template>

        </el-splitter-panel>

    </el-splitter>

</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"
import LeadInfo from "@Page/Lead/Block/LeadInfo.vue";

const props = defineProps({
    new_leads: Array,
    my_leads: Object,
    //work_leads: Array,
    leads: Array,
    title: {
        type: String,
        default: 'Текущие заявки',
    },
    boards: Object,

    staffs: Array,
})

//console.log(props.my_leads)

const dragItem = ref(null);
const dragFrom = ref(null);


function onDropOver(b) {
    // console.log('onDropOver', b)
}


function onDropList(key) {




    router.visit(route('admin.lead.set-status', {lead: dragItem.value.id}), {
        method: "post",
        data: {status: key},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {

        }
    })

    //  console.log(t, dragItem.value.type)
    console.log(t, dragItem.value)

    dragItem.value = null
    dragFrom.value
}

function onDragStart(item, t) {

    dragItem.value = item
    dragFrom.value = t
}

</script>
