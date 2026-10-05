<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Категория {{ category.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <CategoryInfo :category="category"/>

            <HelpBlock v-if="showHelp">
                <p><b>Название категории</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При
                    заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg.
                    Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их
                    пустыми.</p>
            </HelpBlock>
        </div>
        <el-tabs>
            <PanelChildren :category="category"/>
            <PanelAttributes :category-id="category.id"/>
            <PanelProducts :category-id="category.id"/>
            <PanelBlocks :blocks="blocks || []" :category-id="category.id"/>
        </el-tabs>

    </el-config-provider>
</template>

<script setup lang="ts">
import {inject, ref, defineProps, reactive} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";

import CategoryInfo from "./Block/Info.vue";
import PanelChildren from './Panels/Children.vue'
import PanelAttributes from './Panels/Attributes.vue'
import PanelProducts from './Panels/Products.vue'
import PanelBlocks from "./Panels/Blocks.vue";
import HelpBlock from "@Comp/HelpBlock.vue";

const props = defineProps({
    category: Object,
    title: {
        type: String,
        default: 'Карточка категории',
    },
    blocks: Array,
})
const showHelp = ref(false);

</script>

<style scoped>

</style>
