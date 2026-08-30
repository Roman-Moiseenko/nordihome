<template>
    <div class="flex">
        <el-select
            id="select"
            v-model="form.clientId"
            filterable
            remote
            reserve-keyword
            placeholder="Введите Имя, Телефон, ИНН и др."
            :remote-method="remoteMethod"
            :loading="loading"
            style="width: 260px"
            @keyup.enter="onSelect"
        >
            <el-option
                v-for="item in options"
                :key="item.id"
                :value="item.id"
                :label="item.fullName"
            >
                {{ item.fullName }} ({{ item.phone }})
            </el-option>
            <template #loading>
                Загрузка
            </template>
            <template #empty>
                Не найдено
                <el-button size="small" @click="dialogCreate = true">Создать?</el-button>
            </template>
        </el-select>

        <el-button id="button" type="primary" @click="onAdd" class="ml-1">
            <i class="fa-light fa-user mr-2"></i>
            Выбрать
        </el-button>
    </div>
    <AddUser :show="dialogCreate" @update:client="onCreateUser" />
</template>

<script setup lang="ts">

import {reactive, ref} from "vue";
import axios from "axios";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";
import AddUser from "@Comp/User/Add.vue";
import {func} from "@Res/func.js"

const props = defineProps({
    clientId: Number,
    route: String,
})
const form = reactive({
    clientId: null,
})
//Поиск Клиента
const loading = ref(false)
const options = ref([])
const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(route('admin.client.search'), {search: query}).then(response => {
            if (response.data.error !== undefined) console.log(response.data.error)

            options.value = response.data
            loading.value = false
        }).catch(reason => {
            console.log('reason', reason)
        });
    } else {
        options.value = []
    }
}
///<==
function onSelect() {
    document.getElementById('button').focus()
}

//Выбрать клиента
function onAdd() {
    if (form.clientId === null) return;
    router.visit(props.route, {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            form.clientId = null
            document.getElementById('select').focus()
        }
    })
}

//Добавить и выбрать клиента ===>
const dialogCreate = ref(false)
function onCreateUser(val) {
    if (val !== null) {
         router.visit(props.route, {
             method: "post",
             data: {clientId: val},
             preserveScroll: true,
             preserveState: false,
             onSuccess: page => {
                 form.clientId = null
                 dialogCreate.value = false
             },
             onError: page => {
                 console.log('Error', page)
             }
         })
    } else {
        dialogCreate.value = false
    }
}
//<==
</script>
