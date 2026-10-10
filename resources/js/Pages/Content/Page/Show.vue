<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <div class="flex">
            <h1 class="font-medium text-xl">
                Страница {{ page.name }}
            </h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <PageInfo :page="page" :templates="templates"/>

            <HelpBlock v-if="showHelp">
                <p><b>Название страницы</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на страницу) можно не заполнять, тогда оно заполнится автоматически. При
                    заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg.
                    Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для заполнения обязательны.</p>
            </HelpBlock>
        </div>
        <el-tabs>
            <PanelText :page="page" :tiny_api="tiny_api" />
            <PanelBlocks :blocks="blocks || []" :page-id="page.id"/>
        </el-tabs>
    </el-config-provider>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import {Head} from "@inertiajs/vue3";
import PageInfo from "./Block/Info.vue";
import HelpBlock from "@Comp/HelpBlock.vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import PanelText from "./Panels/Text.vue";
import PanelBlocks from "./Panels/Blocks.vue";

const props = defineProps({
    page: Object,
    blocks: Array,
    title: {
        type: String,
        default: 'Карточка Страницы',
    },
    templates: Array,
    tiny_api: String,
})
const showHelp = ref(false);
</script>
