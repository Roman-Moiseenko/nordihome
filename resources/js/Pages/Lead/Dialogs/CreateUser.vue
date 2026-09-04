<template>
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
</template>

<script setup lang="ts">
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"

const dialogUser = ref(false)
const formUser = reactive({
    lead: null,
    surname: null,
    firstname: null,
    secondname: null,
    email: null,
    phone: null,
})

function open(val) {
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
            dialogUser.value = false
        }
    })
}

defineExpose({open})
</script>
