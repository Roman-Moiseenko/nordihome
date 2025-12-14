<template>
    <div class="shadow-lg mb-1 bg-white" style="width: 100%;">
        <div :class="'p-1 ' + (finished ? 'bg-red-600' : 'bg-white')" >
            <el-tag type="info">#{{ lead.id }}</el-tag>
            <el-tag type="danger" effect="plain">{{ func.shortdate(lead.created_at) }}</el-tag>
            <el-tag type="warning">{{ lead.type }}</el-tag>
        </div>
        <div v-if="lead.user">
            <Link :href="route('admin.user.show', {user: lead.user.id})" class="flex items-center w-full text-sm"
                  type="primary"> {{ func.fullName(lead.user.fullname) }} </Link>
        </div>
        <div v-if="!lead.user" class="flex">
            <EditField :field="lead.name" @update:field="onSetName" class="text-sm font-medium"/>
        </div>
        <div v-if="lead.order !== null">
            <Link :href="route('admin.order.show', {order: lead.order.id})" class="flex items-center w-full text-sm"
                  type="primary">Заказ #{{ lead.order.number }} на {{ func.price(lead.order.amount)}} </Link>
        </div>

        <el-collapse :name="'items' + lead.id">
            <el-collapse-item name="1" class="items-lead">
                <template #title >
                    <span v-if="lead.comment">
                            {{ lead.comment }} <el-tag v-if="lead.finished_at" type="danger" effect="dark">{{ func.date(lead.finished_at) }}</el-tag>
                        </span>
                    <span v-else><el-tag type="info" effect="plain">Нет комментариев</el-tag></span>
                </template>
                <div v-for="item in lead.items" class="border border-1 border-dotted p-1">
                    <el-tag type="info" >{{ (item.created_at) }}</el-tag> <br>
                    {{ item.comment }}
                    <el-tag v-if="item.finished_at" type="danger" effect="light">{{ func.date(item.finished_at) }}</el-tag>
                </div>
                <el-tooltip effect="dark" content="Добавить комментарий">
                <el-button  type="warning" @click="onAddItem"><i class="fa-light fa-comment"></i></el-button>

                </el-tooltip>
            </el-collapse-item>
        </el-collapse>

        <el-collapse :name="'data' + lead.id">
            <el-collapse-item title="Данные запроса" name="1">
                <div v-for="field in lead.data">
                    <template v-if="field.slug != 'id'">
                        <el-tag type="info" effect="dark">{{ field.name }}</el-tag>
                        <el-tag type="info">{{ field.value }}</el-tag>
                    </template>
                </div>

            </el-collapse-item>
        </el-collapse>
        <div v-if="lead.status !== 1">
            <el-button  v-if="!lead.user" type="primary" @click="onCreateUser"><i class="fa-light fa-user-plus"></i></el-button>
            <el-button  v-if="!lead.order" type="primary" @click="onCreateOrder"><i class="fa-light fa-cart-plus"></i></el-button>
            <el-button  type="success" @click="onCanceled"><i class="fa-light fa-check"></i></el-button>
            <el-button  type="info" @click="onComleted"><i class="fa-light fa-trash"></i></el-button>

        </div>
    </div>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref, computed} from "vue";
import {func} from "@Res/func.js"
import {Link, router} from "@inertiajs/vue3";
import EditField from "@Comp/Elements/EditField.vue";
import {route} from "ziggy-js";

const props = defineProps({
    lead: Object,
})
console.log(props.lead.items)
const $emit = defineEmits(['create:user', 'create:order', 'add:item'])

const finished = computed( () => props.lead.finished_at == null ? false : ( new Date(props.lead.finished_at) < new Date() ))


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
function onCanceled()
{
    //TODO
}
function onComleted()
{
    //TODO
}
//Редактирование полей
function onSetName(value) {
    router.visit(route('admin.lead.set-name', {lead: props.lead.id}), {
        method: "post",
        data: {name: value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
        }
    })
}
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

</script>
<style scoped>
.el-collapse .items-lead {
    --el-collapse-header-height: 24px;
}
</style>
