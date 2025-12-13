<template>
    <div class="shadow-lg bg-white mb-1" style="width: 100%;">
        <div>
            <el-tag type="info">#{{ item.id }}</el-tag>
            <el-tag type="danger" effect="plain">{{ func.shortdate(item.created_at) }}</el-tag>
            <el-tag type="warning">{{ item.type }}</el-tag>
        </div>
        <div v-if="item.user">
            <Link :href="route('admin.user.show', {user: item.user.id})" class="flex items-center w-full text-sm"
                  type="primary"> {{ func.fullName(item.user.fullname) }} </Link>
        </div>
        <div v-if="!item.user" class="flex">
            <EditField :field="item.name" @update:field="onSetName" class="text-sm font-medium"/>
        </div>
        <div v-if="item.status !== 1">
            <el-tag size="small" type="primary">finish</el-tag>
            <EditField :field="item.finished_at" :isdate="true" @update:field="onSetFinished" class="text-sm font-medium"/>
        </div>
        <div v-if="item.status !== 1">
            <el-tag size="small" type="primary">comment</el-tag>
            <EditField :field="item.comment" :isTextArea="true" :rows="3"
                       @update:field="onSetComment" class="text-sm font-medium"/>
        </div>
        <el-collapse :name="item.id">
            <el-collapse-item title="Данные запроса" name="1">
                <div v-for="field in item.data">
                    <template v-if="field.slug != 'id'">
                        <el-tag type="info" effect="dark">{{ field.name }}</el-tag>
                        <el-tag type="info">{{ field.value }}</el-tag>
                    </template>
                </div>
            </el-collapse-item>
        </el-collapse>
        <div v-if="item.status !== 1">
            <el-button  v-if="!item.user" type="primary" @click="onCreateUser"><i class="fa-light fa-user-plus"></i></el-button>
            <el-button  v-if="!item.order" type="primary" @click="onCreateOrder"><i class="fa-light fa-cart-plus"></i></el-button>
            <el-button  type="success"><i class="fa-light fa-check" @click="onCanceled"></i></el-button>
            <el-button  type="info"><i class="fa-light fa-trash" @click="onComleted"></i></el-button>
        </div>
    </div>
</template>

<script setup lang="ts">
import {defineProps, reactive} from "vue";
import {func} from "@Res/func.js"
import {Link, router} from "@inertiajs/vue3";
import EditField from "@Comp/Elements/EditField.vue";
import {route} from "ziggy-js";

const props = defineProps({
    item: Object,
})
const $emit = defineEmits(['create:user', 'create:order'])

function onCreateUser() {
    const fields = {
        id: props.item.id,
        name: null,
        email: null,
        phone: null
    }
    if (Array.isArray(props.item.data)) {
        props.item.data.forEach(item => {
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
    $emit('create:order', props.item.id)
}
function onCanceled()
{

}
function onComleted()
{

}
//Редактирование полей
function onSetName(value) {
    router.visit(route('admin.lead.set-name', {lead: props.item.id}), {
        method: "post",
        data: {name: value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
function onSetComment(value) {
    router.visit(route('admin.lead.set-comment', {lead: props.item.id}), {
        method: "post",
        data: {comment: value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
function onSetFinished(value) {
    router.visit(route('admin.lead.set-finished', {lead: props.item.id}), {
        method: "post",
        data: {finished_at: func.datetime(value)},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
//console.log(props.item)
</script>
