<template>
    <div class="shadow-lg mb-1 bg-white" style="width: 100%;">
        <BlockInfo :lead="lead" />
        <BlockUser :lead="lead" />
        <CollapseOrder v-if="lead.order !== null" :lead="lead" />
        <CollapseItems :lead="lead" @add:item="onAddItem"/>
        <CollapseData :lead="lead" />
        <CollapseLeads v-if="lead.leads.length > 0" :lead="lead" />

        <!-- ACTION -->
        <div v-if="lead.status !== 1">
            <el-button  v-if="!lead.user" type="primary" @click="onCreateUser"><i class="fa-light fa-user-plus"></i></el-button>
            <el-button  v-if="!lead.order" type="primary" @click="onCreateOrder"><i class="fa-light fa-cart-plus"></i></el-button>
            <el-button  type="info" @click="onCanceled"><i class="fa-light fa-trash"></i></el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

import BlockUser from './BlockUser.vue'
import CollapseItems from './CollapseItems.vue'
import CollapseData from './CollapseData.vue'
import CollapseLeads from './CollapseLeads.vue'
import BlockInfo from "./BlockInfo.vue";
import CollapseOrder from "./CollapseOrder.vue";

const props = defineProps({
    lead: Object,
})
//console.log(props.lead.id, props.lead.leads)
const $emit = defineEmits(['create:user', 'create:order', 'add:item', 'lead:cancel'])

function onCreateUser() {
    const fields = {
        id: props.lead.id,
        name: null,
        email: null,
        phone: null
    }
    if (Array.isArray(props.lead.data)) {
        props.lead.data.forEach(item => {
            if (item.hasOwnProperty('slug')) {
                if (item['slug'] === 'name') fields.name = item['value']
                if (item['slug'] === 'email') fields.email = item['value']
                if (item['slug'] === 'phone') fields.phone = item['value']
            }
        })
    }
    $emit('create:user', fields)
}
function onCreateOrder() {
    $emit('create:order', props.lead.id)
}
function onAddItem() {
    $emit('add:item', props.lead.id)
}
function onCanceled() {
    $emit('lead:cancel', props.lead.id)
}

//Редактирование полей
/*
function onSetComment(value) {
    router.visit(route('admin.lead.set-comment', {lead: props.lead.id}), {
        method: "post",
        data: {comment: value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
function onSetFinished(value) {
    router.visit(route('admin.lead.set-finished', {lead: props.lead.id}), {
        method: "post",
        data: {finished_at: func.datetime(value)},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
*/
</script>

