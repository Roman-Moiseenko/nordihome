<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Логин" :rules="{required: true}">
                    <el-input v-model="form.name" placeholder="Только латиница и цифры" :formatter="val => func.MaskLogin(val)"/>
                    <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                </el-form-item>
                <el-form-item label="Email" :rules="{required: true}">
                    <el-input v-model="form.email" placeholder="80000000000" :formatter="val => func.MaskEmail(val)"/>
                    <div v-if="errors.email" class="text-red-700">{{ errors.email }}</div>
                </el-form-item>
                <el-form-item label="Телефон" :rules="{required: true}">
                    <el-input v-model="form.phone" placeholder="80000000000" :formatter="val => func.MaskPhone(val)"/>
                    <div v-if="errors.phone" class="text-red-700">{{ errors.phone }}</div>
                </el-form-item>
                <el-form-item label="Новый Пароль">
                    <el-input v-model="form.password" type="password" show-password autocomplete="new-password"/>
                    <div v-if="errors.password" class="text-red-700">{{ errors.password }}</div>
                </el-form-item>
                <el-divider/>
                <el-form-item label="ID Телеграм-бота">
                    <el-input v-model="form.telegram_user_id"/>
                    <div v-if="errors.telegram_user_id" class="text-red-700">{{ errors.telegram_user_id }}</div>
                </el-form-item>
                <el-form-item label="Должность" :rules="{required: true}">
                    <el-input v-model="form.post"/>
                    <div v-if="errors.post" class="text-red-700">{{ errors.post }}</div>
                </el-form-item>
                <el-form-item label="Доступ" :rules="{required: true}">
                    <el-select v-model="form.role" placeholder="Select" style="width: 240px">
                        <el-option v-for="item in roles" :key="item.value" :label="item.label" :value="item.value"/>
                    </el-select>
                    <div v-if="errors.role" class="text-red-700">{{ errors.role }}</div>
                </el-form-item>
                <el-divider/>
                <el-form-item label="Фамилия" :rules="{required: true}">
                    <el-input v-model="form.surname"/>
                    <div v-if="errors.surname" class="text-red-700">{{ errors.surname }}</div>
                </el-form-item>
                <el-form-item label="Имя" :rules="{required: true}">
                    <el-input v-model="form.firstname"/>
                    <div v-if="errors.firstname" class="text-red-700">{{ errors.firstname }}</div>
                </el-form-item>
                <el-form-item label="Отчество">
                    <el-input v-model="form.secondname"/>
                    <div v-if="errors.secondname" class="text-red-700">{{ errors.secondname }}</div>
                </el-form-item>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Фото сотрудника на аватар"
                v-model:image="staff.photo"
                @selectImageFile="onSelectImage"
            />
        </el-col>
    </el-row>

    <el-button type="info" @click="onClose" style="margin-left: 4px">
        Отмена
    </el-button>
    <el-button type="success" @click="onSave">
        Сохранить
    </el-button>

</template>

<script setup lang="ts">
import {defineProps, defineEmits, reactive} from 'vue'
import {func} from "@Res/func"
import UploadImageFile from "@Comp/UploadImageFile.vue";
import { router } from "@inertiajs/vue3"

const props = defineProps({
    staff: Object,
    errors: Object,
    responsibilities: Array,
    roles: Array,
})

const form = reactive({
    name: props.staff.name,
    phone: props.staff.phone,
    email: props.staff.email,
    password: null,
    role: props.staff.role,
    post: props.staff.post,
    telegram_user_id: props.staff.telegram_user_id,
    surname: props.staff.fullname.surname,
    firstname: props.staff.fullname.firstname,
    secondname: props.staff.fullname.secondname,
    file: null,
    close: null,
    _method: 'put',
    clear_file: false, //Удалить загруженное ранее фото
})
function onSelectImage(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
}
function onClose() {
    $emit('update:show', false)
}
function onSave(val) {
    form.close = val
    router.visit(route('admin.staff.update', {staff: props.staff.id}), {
        method: 'post',
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            $emit('update:show', false)
        }
    });
}
const $emit = defineEmits(['update:show'])

function closeEdit() {
    $emit('update:show', false)
}
</script>
<style scoped>

</style>
