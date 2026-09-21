<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Фид {{ feed.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <FeedInfo :feed="feed"/>

            <HelpBlock v-if="showHelp">
                <p><b>Название</b> магазина для Яндекс и Гугл маркета.</p>
                <p>Если <b>Название и Описание</b> - не заполнены, то берутся из настроек Продавца.</p>
                <p>Остальные <b>Поля</b> будут появляться по мере необходимости.</p>
            </HelpBlock>
        </div>

        <Price :feed="feed"/>
        <Products :feed="feed" :save="save"/>
        <Tags :feed="feed" :save="save"/>
        <Categories :feed="feed" :save="save"/>
        <Rooms :feed="feed" :save="save"/>
        <Promotions :feed="feed" :save="save"/>
        <Groups :feed="feed" :save="save"/>
    </el-config-provider>
</template>

<script setup lang="ts">
import {computed, defineProps, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router, usePage} from "@inertiajs/vue3";
import HelpBlock from "@Comp/HelpBlock.vue";
import FeedInfo from "./Block/Info.vue"
import Price from "./Block/Price.vue"
import Products from "./Block/Products.vue"
import Tags from "./Block/Tags.vue"
import Categories from "./Block/Categories.vue"
import Rooms from "./Block/Rooms.vue"
import Promotions from "./Block/Promotions.vue"
import Groups from "./Block/Groups.vue"

const props = defineProps({
    feed: Object,
    title: {
        type: String,
        default: 'Карточка фида',
    },
})

const page = usePage()
const feed = computed(() => page.props.feed)
const showHelp = ref(false)

/**
 * Единая точка сохранения: отправляем только изменяемый параметр
 * (field + action add|remove|clear + направление in|out + ids).
 */
function save(field: string, action: string, _in: boolean, ids: number[] = []) {
    router.visit(route('admin.output.feed.update', {feed: props.feed.id}), {
        only: ['feed', 'flash'],
        method: "post",
        data: {field, action, in: _in, ids},
        preserveScroll: true,
        preserveState: true,
    })
}
</script>
