<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">Редактировать письмо</h1>
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
                    :api-key="tiny_api" v-model="form.message"
                    :init="store.tiny"
                />
                <div v-if="errors.message" class="text-red-700">{{ errors.message }}</div>
            </div>
            <el-upload
                ref="upload" action="#"
                v-model:file-list="fileList"
                class="lg:w-80"
                :on-remove="handleRemove"
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
            <el-button type="primary" @click="onSubmitSend">Сохранить и Отправить</el-button>
            <div v-if="isUnSave" class="text-red-700">Были внесены изменения, данные не сохранены</div>
        </el-form>
    </div>
</template>


<script lang="ts" setup>
import {Head} from '@inertiajs/vue3'
import {reactive, ref, watch} from 'vue'
import {router} from "@inertiajs/vue3";
import {func} from "@Res/func.js"
import {UploadUserFile, UploadInstance} from "element-plus";
import {useStore} from '@Res/store.js'
import Editor from '@tinymce/tinymce-vue'

const store = useStore();
const props = defineProps({
    errors: Object,
    outbox: Object,
    tiny_api: String,
    title: {
        type: String,
        default: 'Редактирование письма',
    },
});
const fileList = ref<UploadUserFile[]>([]);
for (let key in props.outbox.attachments) {
    fileList.value.push({
        name: key,
        url: props.outbox.attachments[key],
    });
}
const form = reactive({
    emails: props.outbox.emails,
    subject: props.outbox.subject,
    message: props.outbox.message,
    attachments: [],
    send: false,
    _method: 'put',
})
///Блок сохранения и обновления=>
const isUnSave = ref(false)
watch(
    () => ({...form}),
    function (newValue, oldValue) {
        isUnSave.value = true
    },
    {deep: true}
);

function onDelAttach(val) {
    router.post(route('admin.mail.outbox.delete-attachment', {outbox: props.outbox.id}), {name: val})
}
function onSubmit() {
    router.post(route('admin.mail.outbox.update', {outbox: props.outbox.id}), form)
    isUnSave.value = false
}
function onSubmitSend() {
    form.send = true
    router.post(route('admin.mail.outbox.update', {outbox: props.outbox.id}), form)
    isUnSave.value = false
}
function handleRemove(val) {
    if (val.url !== undefined) {
        router.post(route('admin.mail.outbox.delete-attachment', {outbox: props.outbox.id}), {
            file: val.name,
            url: val.url
        })
    }
}
</script>

