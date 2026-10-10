<script setup lang="ts">

import ContentBlockEditor from "@Comp/Content/ContentBlock/ContentBlockEditor.vue";
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";
import {defineProps, ref} from "vue";
import {useStore} from "@Res/store";
const store = useStore();

const props = defineProps({
    page: Object,
    tiny_api: String,
})

const text = ref(props.page.text)

function saveText() {
    axios.post(route('admin.content.page.update-text', {id: props.page.id}), {text: text.value}).then(resolve => {
    }).catch(reason => {
        console.log(reason)
    })
}
</script>

<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-file-text"></i>
                <span> Текст</span>
            </span>
        </template>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <editor
                :api-key="tiny_api" v-model="text"
                :init="store.tiny"
                @change="saveText"
            />
        </div>
    </el-tab-pane>
</template>

<style scoped>

</style>
