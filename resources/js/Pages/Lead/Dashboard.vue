<template>
    <Head><title>{{ title }}</title></Head>

    <el-row>
        <div class="mt-2 p-5 bg-white rounded-md">
            Фильтры
        </div>
    </el-row>
    <el-splitter>

        <!-- Неразобранные -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(1)"
            @drop="onDropList(1)"
            class="shadow-sm bg-green-100"
        >
            <el-tag effect="dark" type="success" size="large">НОВЫЕ</el-tag>
            <template v-for="item in new_leads">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 1)"/>
            </template>
        </el-splitter-panel>

        <!-- В работе -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(2)"
            @drop="onDropList(2)"
            class="shadow-sm bg-red-100"
        >
            <el-tag effect="dark" type="danger" size="large">МОИ В РАБОТЕ</el-tag>
            <template v-for="item in my_leads.in_work">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 2)"/>
            </template>
        </el-splitter-panel>

        <!-- Клиент думает -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(3)"
            @drop="onDropList(3)"
            class="shadow-sm bg-orange-100"
        >
            <el-tag effect="dark" type="info" size="large">КЛИЕНТ ДУМАЕТ</el-tag>
            <template v-for="item in my_leads.not_decide">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 3)"/>
            </template>
        </el-splitter-panel>

        <!-- Выставлен счет -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(4)"
            @drop="onDropList(4)"
            class="shadow-sm bg-cyan-100"
        >
            <el-tag effect="dark" type="info" size="large">ВЫСТАВЛЕН СЧЕТ</el-tag>
            <template v-for="item in my_leads.invoice">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 4)"/>
            </template>
        </el-splitter-panel>

        <!-- Оплачен -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(5)"
            @drop="onDropList(5)"
            class="shadow-sm bg-lime-100"
        >
            <el-tag effect="dark" type="info" size="large">ОПЛАЧЕН</el-tag>
            <template v-for="item in my_leads.paid">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 5)"/>
            </template>
        </el-splitter-panel>

        <!-- На сборке -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(6)"
            @drop="onDropList(6)"
            class="shadow-sm bg-slate-100"
        >
            <el-tag effect="dark" type="info" size="large">НА СБОРКЕ</el-tag>
            <template v-for="item in my_leads.assembly">
                <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 6)"/>
            </template>
        </el-splitter-panel>

        <!-- На доставке -->
        <el-splitter-panel
            @dragover.prevent="onDropOver(7)"
            @drop="onDropList(7)"
            class="shadow-sm bg-stone-100"
        >
            <el-tag effect="dark" type="info" size="large">НА ДОСТАВКЕ</el-tag>
            <template v-for="item in my_leads.delivery">
                    <LeadInfo :item="item" draggable="true" @dragstart="onDragStart(item, 7)"/>
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
  //  console.log(t, dragItem.value)

    dragItem.value = null
    dragFrom.value
}

function onDragStart(item, t) {

    dragItem.value = item
    dragFrom.value = t
}

</script>
