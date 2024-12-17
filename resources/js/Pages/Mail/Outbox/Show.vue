<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl"> {{ outbox.name }} </h1>

    <div class="mt-3 p-3 bg-white rounded-lg ">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="Получатели">
                        <el-tag type="info" class="mr-1" v-for="item in outbox.emails">{{ item }}</el-tag>
                    </el-descriptions-item>
                    <el-descriptions-item label="Тема">{{ outbox.subject }}</el-descriptions-item>
                    <el-descriptions-item label="Создано">{{ outbox.created_at }}</el-descriptions-item>
                    <el-descriptions-item label="Отправлено">{{ outbox.sent_at }}</el-descriptions-item>
                </el-descriptions>
            </div>
            <div class="p-2">
                <div class="font-medium mb-2">Вложенные файлы:</div>
                <div v-for="(item, index) in outbox.attachments" class="ml-1 mt-1">
                    <el-tag type="primary" class="font-medium cursor-pointer" @click="download(item, index)" title="Скачать файл">
                        {{ index }}&nbsp;<el-icon><Download /></el-icon>
                    </el-tag>
                </div>
            </div>
        </div>


        <div class="mt-3 flex flex-row">
            <el-button v-if="!outbox.sent" type="info" @click="goEdit">Редактировать</el-button>
            <el-button v-if="!outbox.sent" type="primary" @click="goSend">Отправить</el-button>
        </div>
    </div>

    <h2 class="mt-3 font-medium">Письмо:</h2>
    <div class="mt-1 p-3 bg-white rounded-lg" v-html="outbox.message">
    </div>

</template>

<script lang="ts" setup>
import {Head, router} from '@inertiajs/vue3'
import axios from "axios";

const props = defineProps({
    outbox: Object,
    title: {
        type: String,
        default: 'Карточка письма',
    },
});

function goEdit() {
    router.get(route('admin.mail.outbox.edit', {outbox: props.outbox.id}));
}
function goSend() {
    router.post(route('admin.mail.outbox.send', {outbox: props.outbox.id}));
}
function download(file, name) {
    axios.get(route('admin.mail.outbox.attachment'),
        {responseType: 'arraybuffer', params: {file: file}}
    ).then(res=>{
        let blob = new Blob([res.data], {type:'application/*'})
        let link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = name
        link._target = 'blank'
        link.click();
    })
}
</script>

