<template>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">
                Запись {{ post.name }}
            </h1>
            <el-tooltip  content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <InfoPost :post="post" :templates="templates"/>
            <HelpBlock v-if="showHelp">
                <p><b>Название записи</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на рубрику) можно не заполнять, тогда оно заполнится автоматически. При
                    заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg.
                    Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для заполнения обязательны.</p>
            </HelpBlock>
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
import HelpBlock from "@Comp/HelpBlock.vue";

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
const text = ref(props.post.text)

const showHelp = ref(false);
function saveText() {
    axios.post(route('admin.content.post.set-text', {post: props.post.id}), {text: text.value}).then(resolve => {
    }).catch(reason => {
        console.log(reason)
    })
}
</script>

<style scoped>

</style>
