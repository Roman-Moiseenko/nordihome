<template>
    <div class="flex">
        <el-select
            id="select"
            v-model="form.user_id"
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
                :label="item.public_name + ' ('+ item.phone + ')'"
            >
                {{ item.public_name }} ({{ item.phone }})
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
    <AddUser :show="dialogCreate" @update:user="onCreateUser" />
</template>

<script setup lang="ts">

import {reactive, ref} from "vue";
import axios from "axios";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";
import AddUser from "@Comp/User/Add.vue";

const props = defineProps({
    user_id: Number,
    route: String,
})
const form = reactive({
    user_id: null,
})
//Поиск Клиента
const loading = ref(false)
const options = ref([])
const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(route('admin.user.search'), {search: query}).then(response => {
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
    if (form.user_id === null) return;
    router.visit(props.route, {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            form.user = null
            document.getElementById('select').focus()
        }
    })
}

//Добавить и выбрать клиента ===>
const dialogCreate = ref(false)
function onCreateUser(val) {
    console.log(props.route)
    if (val !== null) {
         router.visit(props.route, {
             method: "post",
             data: {user_id: val},
             preserveScroll: true,
             preserveState: false,
             onSuccess: page => {
                 form.user = null
                 dialogCreate.value = false
             },
             onError: page => {
                 console.log('Error', page)
             }
         })
    }
}
//<==
</script>
