<template>
    <el-tab-pane>
        <template #label>Парсер габаритов</template>
        <el-button type="primary" dark class="mt-2" @click="onParsing">Спарсить</el-button>
        <div>
            <el-input v-model="codes" type="textarea" :rows="16" style="width: 350px;"/>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import axios from 'axios'
import {ElLoading} from "element-plus";

const codes = ref(null)

function onParsing() {

    const loading = ElLoading.service({
        lock: false,
        text: 'Создание документа',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    axios.post(route('admin.nordihome.parser-dimensions'), null,
        {
            responseType: 'arraybuffer',
            params: {codes: codes.value},
        }
    ).then(response => {
        let blob = new Blob([response.data], {type: 'application/*'})
        let link = document.createElement('a')
        let headers = response.headers

        link.href = window.URL.createObjectURL(blob)
        link.download = headers['filename']
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        loading.close()
        codes.value = null
        URL.revokeObjectURL(link.href)
    }).catch(reason => {
        loading.close()
    })
}
</script>

<style scoped>

</style>
