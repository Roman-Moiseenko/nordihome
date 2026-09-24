<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Бренд товаров {{ brand.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <BrandInfo :brand="brand" :parsers="parsers" :currencies="currencies"/>

            <HelpBlock v-if="showHelp">
                <p><b>Название Бренда</b> является обязательным полем.</p>
                <p>Поле <b>Ссылка на сайт</b> используется в Schema для SEO.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку 700х700.</p>
                <p>Если используется <b>Парсер</b>, то Ссылка на тот сайт, который парсится.</p>
            </HelpBlock>
        </div>

        <el-tabs>
            <PanelProducts :brand="brand" />
            <PanelBlocks :blocks="blocks || []" :brand-id="brand.id"/>
        </el-tabs>
    </el-config-provider>
</template>
<script lang="ts" setup>
import {ref, defineProps} from "vue";
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import BrandInfo from  './Block/Info.vue'
import HelpBlock from "@Comp/HelpBlock.vue";
import PanelProducts from "./Panels/Products.vue";
import PanelBlocks from "./Panels/Blocks.vue";

const props = defineProps({
    brand: Object,
    parsers: Array,
    title: {
        type: String,
        default: 'Карточка бренда товаров',
    },
    currencies: Array,
    blocks: Array,
})

const showHelp = ref(false);

</script>
