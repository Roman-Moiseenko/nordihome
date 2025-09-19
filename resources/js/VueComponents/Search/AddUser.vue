
<template>
    <div v-if="user_id">
        Клиент назначен. Менять данные
    </div>
    <div v-else>
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
                    Не найдено <el-button size="small" @click="createUser">Создать?</el-button>
                </template>
            </el-select>

            <el-button id="button" type="primary" @click="onAdd" class="ml-1">
                <i class="fa-light fa-user mr-2"></i>
                Выбрать
            </el-button>
        </div>

        <el-dialog v-model="dialogCreate" title="Новый клиент" width="400">
            <el-form label-width="auto">
                <el-form-item label="Клиент">
                    <el-input v-model="formCreate.name" />
                </el-form-item>

                <el-button type="info" class="" @click="dialogCreate = false">
                    Отмена
                </el-button>
                <el-button type="primary" class="" @click="storeUser">
                    Создать
                </el-button>
            </el-form>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">

import {reactive, ref} from "vue";
import axios from "axios";
import {ElLoading} from "element-plus";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    user_id: Number,
    route: String,
})
const form = reactive({
    user_id: null,
})
const loading = ref(false)
const options = ref([])
const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(route('admin.user.search'), {search: query}).then(response => {
            console.log('responser', response)
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
function onSelect() {
    document.getElementById('button').focus()
}


function onAdd() {
    if (form.product_id === null) return;

    router.visit(props.route, {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            form.user = null
            //props.user_id
            document.getElementById('select').focus()
        }
    })
}
//Доб.клиента ===>
const dialogCreate = ref(false)
const formCreate = reactive({
    name: null,
})
function createUser() {
    dialogCreate.value = true

}

function storeUser() {
   /* axios.post(route('admin.product.fast-create'), formCreate).then(response => {
        form.product_id = response.data
        onAdd()
        dialogCreate.value = false
    });*/
}
</script>
