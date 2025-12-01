<template>
    <Head><title>{{ $props.title }}</title></Head>
    <h1 class="font-medium text-xl">Написать письмо</h1>
    <div class="mt-3 p-3 bg-white rounded-lg">
        <el-form :model="form" label-width="auto">

            <el-form-item label="Получатели" :rules="{required: true}">
                <el-select
                    v-model="form.emails"
                    multiple
                    filterable
                    allow-create
                    default-first-option
                    :reserve-keyword="false"
                    placeholder="Добавьте почту"
                >
                    <el-option
                        v-for="item in form.emails"
                        :key="item"
                        :label="item"
                        :value="item"
                    />
                </el-select>
                <div v-if="errors.emails" class="text-red-700">{{ errors.emails }}</div>
            </el-form-item>

            <el-form-item label="Тема письма" :rules="{required: true}">
                <el-input v-model="form.subject" placeholder="Название"/>
                <div v-if="errors.subject" class="text-red-700">{{ errors.subject }}</div>
            </el-form-item>

            <div class="w-full mt-3 mb-5">
                <!-- TinyMCE -->
                <editor
                    :api-key="$props.tiny_api" v-model="form.message"
                    :init="store.tiny"
                />
                <div v-if="errors.message" class="text-red-700">{{ errors.message }}</div>
            </div>

            <el-upload
                ref="upload" action="#"

                class="lg:w-80"
                :auto-upload="false"
                multiple
                @input="form.attachments = $event.target.files"
            >
                <template #trigger>
                    <el-button type="info" circle>
                        <el-icon>
                            <Paperclip/>
                        </el-icon>
                    </el-button>
                </template>
                <template #tip>
                    <div class="el-upload__tip">
                        Выберите один или несколько файлов
                    </div>
                </template>
            </el-upload>


            <el-button type="info" @click="onSubmit">Сохранить</el-button>
            <el-button type="primary" @click="onSubmitSend">Отправить</el-button>
            <div v-if="form.isDirty">Изменения не сохранены</div>
        </el-form>
    </div>
</template>


<script lang="ts" setup>
import {Head} from '@inertiajs/vue3'
import {reactive, ref} from 'vue'
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"
import {useStore} from '@Res/store.js'
import {UploadUserFile, UploadInstance} from "element-plus";
import Editor from '@tinymce/tinymce-vue'

const store = useStore();

const props = defineProps({
    errors: Object,
    title: {
        type: String,
        default: 'Новое письмо',
    },
    tiny_api: String,
    email: String,
    subject: String,
});
const upload = ref<UploadInstance>();
const fileList = ref<UploadUserFile[]>();
const form = reactive({
    emails: [],
    subject: props.subject,
    message: null,
    attachments: [],
    send: false,
})
if (props.email !== '') form.emails.push(props.email);

function onSubmit() {
    router.post(route('admin.mail.outbox.store'), form)
}
function onSubmitSend() {
    form.send = true;
    router.post(route('admin.mail.outbox.store'), form)
}
</script>

