<template>
    <el-dropdown v-if="$printed" class="ml-3">
        <el-button type="warning" plain>
            <i class="fa-light fa-print"></i>
        </el-button>
        <template #dropdown class="m-2 accounting-print">
            <div class="m-2 accounting-print">
                <div v-for="item in $printed" class="mt-1">
                    <el-tag class="cursor-pointer" type="warning" @click="getReport(item)">
                        {{item.label }}
                    </el-tag>
                </div>
            </div>
        </template>
    </el-dropdown>
</template>

<script setup>
import {inject, defineProps} from 'vue'
import {Link, router} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({})
//Список для печати/генерации
const $printed = inject('$printed', [])
const $accounting = inject('$accounting')

function getReport(val) {
    console.log(val)
    axios.post(route('admin.report'),null,
        {
            responseType: 'arraybuffer',
            params: {class: val.class, method: val.method, id: $accounting.id},
        }
    ).then(response => {
       console.log('response', response)

        let blob = new Blob([response.data], {type: 'application/*'})
        let link = document.createElement('a')
        let headers = response.headers
        //console.log('headers = ', headers)
        link.href = window.URL.createObjectURL(blob)
        link.download = headers['filename']
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        URL.revokeObjectURL(link.href)
    }).catch(reason => {
        console.log('reason', reason)
    })
}


</script>

<style lang="scss">
.accounting-print {
    a {
        font-size: 13px !important;
    }
}
</style>
