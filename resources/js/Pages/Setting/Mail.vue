<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">{{ title }}</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">
            <el-row :gutter="10">
                <el-col :span="8">
                    <h2 class="font-medium text-lg mb-3">Входящая почта</h2>
                    <el-form-item label="Почтовый домен" :rules="{required: true}">
                        <el-input v-model="form.mail_domain" placeholder="example.com"/>
                        <div v-if="errors.mail_domain" class="text-red-700">{{ errors.mail_domain }}</div>
                    </el-form-item>
                    <el-form-item label="Имя ящика" :rules="{required: true}">
                        <el-input v-model="form.inbox_name" placeholder="без @ и домена"/>
                        <div v-if="errors.inbox_name" class="text-red-700">{{ errors.inbox_name }}</div>
                    </el-form-item>
                    <el-form-item label="Пароль" :rules="{required: true}">
                        <el-input v-model="form.inbox_password" type="password"
                                  placeholder="Please input password"
                                  show-password/>
                        <div v-if="errors.inbox_password" class="text-red-700">{{ errors.inbox_password }}</div>
                    </el-form-item>
                    <el-form-item label="Удалять почту после получения">
                        <el-checkbox v-model="form.inbox_delete"/>
                    </el-form-item>

                </el-col>
                <el-col :span="8">
                    <h2 class="font-medium text-lg mb-3">Исходящая почта</h2>
                    <el-form-item label="Имя ящика" :rules="{required: true}">
                        <el-input v-model="form.outbox_name" placeholder="без @ и домена" autocomplete="new-password"/>
                        <div v-if="errors.outbox_name" class="text-red-700">{{ errors.outbox_name }}</div>
                    </el-form-item>
                    <el-form-item label="Пароль" :rules="{required: true}">
                        <el-input v-model="form.outbox_password" type="password" autocomplete="new-password"
                                  placeholder="Please input password"
                                  show-password/>
                        <div v-if="errors.outbox_password" class="text-red-700">{{ errors.outbox_password }}</div>
                    </el-form-item>
                    <el-form-item label="От кого" :rules="{required: true}">
                        <el-input v-model="form.outbox_from" placeholder="Имя, Компания"/>
                        <div v-if="errors.outbox_from" class="text-red-700">{{ errors.outbox_from }}</div>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <h2 class="font-medium text-lg mb-3">Системная почта</h2>
                    <el-form-item label="Имя ящика" :rules="{required: true}">
                        <el-input v-model="form.system_name" placeholder="без @ и домена" autocomplete="new-password"/>
                        <div v-if="errors.system_name" class="text-red-700">{{ errors.system_name }}</div>
                    </el-form-item>
                    <el-form-item label="Пароль" :rules="{required: true}">
                        <el-input v-model="form.system_password" type="password" autocomplete="new-password"
                                  placeholder="Please input password"
                                  show-password/>
                        <div v-if="errors.system_password" class="text-red-700">{{ errors.system_password }}</div>
                    </el-form-item>
                    <el-form-item label="От кого" :rules="{required: true}">
                        <el-input v-model="form.system_from" placeholder="Имя, Компания"/>
                        <div v-if="errors.system_from" class="text-red-700">{{ errors.system_from }}</div>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-button type="primary" @click="onSubmit">Сохранить</el-button>
            <div v-if="form.isDirty">Изменения не сохранены</div>
        </el-form>
    </div>
</template>


<script lang="ts" setup>
import {reactive} from 'vue'
import {Head, router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"

const props = defineProps({
    errors: Object,
    mail: Object,
    title: {
        type: String,
        default: 'Настройки почты',
    }
});

const form = reactive({
    slug: 'mail',
    mail_domain: props.mail.mail_domain,
    inbox_name: props.mail.inbox_name,
    inbox_password: props.mail.inbox_password,
    inbox_delete: props.mail.inbox_delete,

    outbox_name: props.mail.outbox_name,
    outbox_password: props.mail.outbox_password,
    outbox_from: props.mail.outbox_from,
    system_name: props.mail.system_name,
    system_password: props.mail.system_password,
    system_from: props.mail.system_from,

    /**
     * Добавить новые поля
     */
})

function onSubmit() {
    router.put(route('admin.setting.update'), form)
}
</script>

