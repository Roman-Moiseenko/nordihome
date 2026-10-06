<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Атрибут {{ attribute.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <AttributeInfo :attribute="attribute"/>

            <HelpBlock v-if="showHelp">
                <p><b>Название атрибута</b> не является уникальным полем, для несмежных категорий оно может
                    совпадать.</p>
                <p>Поле <b>категория</b> привязывает атрибут к категории и его дочерним категориям.</p>
                <p class="mt-2">Поле <b>группа</b> позволяет сгруппировать характеристики на странице товара.</p>
                <p class="mt-2">Для <b>картинок</b> используйте форматы с прозрачным фоном и размером не более
                    200х200. Рекомендуем использовать SVG-файлы</p>

                <p>Для типа <b>варианты</b> к каждому значению варианта атрибута предусмотрена возможность
                    установления
                    изображения, например для цвета. Привязать изображение к варианту можно после сохранения
                    атрибута в режиме просмотра.
                </p>
            </HelpBlock>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'

import {defineProps, ref} from 'vue'
import {Head} from "@inertiajs/vue3";
import AttributeInfo from "./Block/Info.vue";
import HelpBlock from "@Comp/HelpBlock.vue";

const props = defineProps({
    attribute: Object,
    title: {
        type: String,
        default: 'Карточка атрибута',
    },
})
const showHelp = ref(false);

</script>

<style scoped>

</style>
