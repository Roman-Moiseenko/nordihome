<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Группа товаров {{ group.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <GroupInfo :group="group" />

            <HelpBlock v-if="showHelp">
                <p><b>Название Группы</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на группу) можно не заполнять, тогда оно заполнится автоматически.
                    При заполнении использовать латинский алфавит. Ссылка используется, если у группы есть своя
                    страница на стороне клиента.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку 700х700.</p>
            </HelpBlock>
        </div>

        <el-tabs>
            <PanelProducts :group="group" />
            <PanelBlocks :blocks="blocks || []" :group-id="group.id"/>
        </el-tabs>
    </el-config-provider>
</template>
<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import GroupInfo from  './Block/Info.vue'
import HelpBlock from "@Comp/HelpBlock.vue";
import PanelProducts from "./Panels/Products.vue";
import PanelBlocks from "./Panels/Blocks.vue";

const props = defineProps({
    group: Object,
    title: {
        type: String,
        default: 'Карточка группы товаров',
    },
    blocks: Array,
})

const showHelp = ref(false);

</script>
