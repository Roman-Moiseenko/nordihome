<template>
    <Head><title>{{ title }}</title></Head>

    <el-row>
        <div class="mt-2 p-5 bg-white rounded-md">
            Фильтры
        </div>
    </el-row>

    <el-splitter>
        <el-splitter-panel v-for="(key, index) in boards"
                           @dragover.prevent="onDropOver(index)"
                           @drop="onDropList(index)"
                           :class="'shadow-sm ' + background[index]"
        >
            <el-tag effect="dark" :type="button_color[index]" size="large">{{ key }}</el-tag>
            <template v-for="item in leads[index]">
                <LeadInfo :item="item"
                          draggable="true" @dragstart="onDragStart(item, index)"
                          @create:user="onDialogUser" @create:order="onDialogOrder"/>
            </template>
        </el-splitter-panel>
    </el-splitter>
    <el-dialog v-model="dialogUser" title="Добавить Клиента" width="400">
        <el-form label-width="auto">
            <el-form-item label="Фамилия">
                <el-input v-model="formUser.surname"/>
            </el-form-item>
            <el-form-item label="Имя">
                <el-input v-model="formUser.firstname"/>
            </el-form-item>
            <el-form-item label="Отчество">
                <el-input v-model="formUser.secondname"/>
            </el-form-item>
            <el-form-item label="Email">
                <el-input v-model="formUser.email" :formatter="val => func.MaskEmail(val)"/>
            </el-form-item>
            <el-form-item label="Телефон">
                <el-input v-model="formUser.phone" :formatter="val => func.MaskPhone(val)"/>
            </el-form-item>

        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="info" class="" @click="dialogUser = false">
                    Отмена
                </el-button>
                <el-button type="primary" class="" @click="onCreateUser">
                    Создать
                </el-button>
            </div>
        </template>
    </el-dialog>

    <el-dialog v-model="dialogOrder" title="Создать заказ" width="400">
        <div class="flex justify-center mb-4 mt-2">
            Создать <el-tag type="danger" class="mx-2">Новый заказ</el-tag> ?
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button type="info" class="" @click="dialogOrder = false">
                    Отмена
                </el-button>
                <el-button type="primary" class="" @click="onCreateOrder">
                    Создать
                </el-button>
            </div>
        </template>
    </el-dialog>

</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"
import LeadInfo from "@Page/Lead/Block/LeadInfo.vue";

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
    1: 'bg-green-100',
    2: 'bg-red-100',
    3: 'bg-orange-100',
    4: 'bg-cyan-100',
    5: 'bg-lime-100',
    6: 'bg-slate-100',
    7: 'bg-stone-100',
}
const button_color = {
    1: 'success',
    2: 'danger',
    3: 'warning',
    4: 'info',
    5: 'info',
    6: 'info',
    7: 'info',
}
const dragItem = ref(null);
const dragFrom = ref(null);

const dialogUser = ref(false)
const dialogOrder = ref(false)
const formUser = reactive({
    lead: null,
    surname: null,
    firstname: null,
    secondname: null,
    email: null,
    phone: null,
})
const formOrder = reactive({
    lead: null,
})

function onDialogUser(val) {
    formUser.lead = val.id
    formUser.firstname = val.name
    formUser.email = val.email
    formUser.phone = val.phone
    dialogUser.value = true
}

function onCreateUser() {
    router.visit(route('admin.lead.create-user', {lead: formUser.lead}), {
        method: "post",
        data: formUser,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogUser.value = true
        }
    })
}

function onDialogOrder(val) {
    console.log(val)
    formOrder.lead = val
    dialogOrder.value = true
}

function onCreateOrder() {
    router.visit(route('admin.lead.create-order', {lead: formOrder.lead}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogUser.value = true
        }
    })
}

//Drag & Drop
function onDropOver(b) {
    // console.log('onDropOver', b)
}

function onDropList(key) {
    if (dragFrom.value === key) return;
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
