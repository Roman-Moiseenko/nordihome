<template>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">
            Запись {{ post.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <InfoPost :post="post" :templates="templates" />
        </div>

        <div class="mt-3 p-3 bg-white rounded-lg ">
            <!-- TinyMCE -->
            <editor
                :api-key="tiny_api" v-model="text"
                :init="store.tiny"
                @change="saveText"
            />
        </div>
        <!-- ContentBlock Editor -->
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ContentBlockEditor
                :blocks="blocks || []"
                :container-id="post.id"
                container-type="post"
            />
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import Editor from '@tinymce/tinymce-vue'
import {defineProps, ref} from "vue";
import {Head, router} from "@inertiajs/vue3";
import InfoPost from "./Block/InfoPost.vue";
import ContentBlockEditor from "../../../VueComponents/Content/ContentBlock/ContentBlockEditor.vue";
import {useStore} from '@Res/store.js'
import axios from "axios";
import ru from 'element-plus/dist/locale/ru.mjs'

const store = useStore();

const props = defineProps({
    post: Object,
    title: {
        type: String,
        default: 'Карточка Записи',
    },
    templates: Array,
    tiny_api: String,
    blocks: Array,
})
console.log(props.blocks);
const text = ref(props.post.text)

function saveText() {
    axios.post(route('admin.content.post.set-text', {post: props.post.id}), {text: text.value}).then(resolve => {
    }).catch(reason => {
        console.log(reason)
    })
}
</script>

<style scoped>

</style>
