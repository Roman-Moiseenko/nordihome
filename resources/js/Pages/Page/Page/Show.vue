<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Страница {{ page.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PageInfo :page="page" :templates="templates" :pages="parent_pages"/>
        </div>

        <div class="mt-3 p-3 bg-white rounded-lg ">
            <!-- TinyMCE -->
            <editor
                :api-key="tiny_api" v-model="text"
                :init="store.tiny"
                @change="saveText"
            />
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'
import {defineProps, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import PageInfo from "./Block/Info.vue";
import {useStore} from '@Res/store.js'
import axios from "axios";
import {resolve} from "chart.js/helpers";

const store = useStore();

const props = defineProps({
    page: Object,
    title: {
        type: String,
        default: 'Карточка Страницы',
    },
    templates: Array,
    parent_pages: Array,
    tiny_api: String,
})
const text = ref(props.page.text)

function saveText() {
    //console.log(text.value)
    axios.post(route('admin.page.page.set-text', {page: props.page.id}), {text: text.value}).then(resolve => {
      //  console.log(resolve)
    }).catch(reason => {
        console.log(reason)
    })
}
</script>
