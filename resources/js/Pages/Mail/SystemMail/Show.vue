<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl"> {{ mail.name }} </h1>

    <div class="mt-3 p-3 bg-white rounded-lg">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="Служба">{{ mail.mailable }}</el-descriptions-item>
                    <el-descriptions-item label="Заголовок">{{ mail.title }}</el-descriptions-item>
                    <el-descriptions-item label="Получатель">{{ mail.user }}</el-descriptions-item>
                    <el-descriptions-item label="Отправлено">{{ mail.created_at }}</el-descriptions-item>
                </el-descriptions>
            </div>
            <div class="p-2">
                <div class="truncate sm:whitespace-normal items-center mt-2">
                    Вложения:&nbsp;
                    <div v-for="(item, index) in mail.attachments" class="ml-1 mt-1">
                        <el-tag type="primary"  class="font-medium cursor-pointer" @click="download(item, index)" title="Скачать файл">
                            {{ index }}&nbsp;<el-icon><Download /></el-icon>
                        </el-tag>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-html="mail.content" class="mt-3 p-3 bg-white rounded-lg">
    </div>

</template>

<script lang="ts" setup>
import {Head, Link} from '@inertiajs/vue3'
import axios from "axios";

const props = defineProps({
    mail: Object,
    title: {
        type: String,
        default: 'Карточка системного сообщения',
    },
});
console.log(props.mail)
function download(file, name) {
    axios.get(route('admin.mail.system.attachment'),
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

