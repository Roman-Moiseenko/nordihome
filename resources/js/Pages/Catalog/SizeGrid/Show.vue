<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
            <h1 class="font-medium text-xl">Размерная сетка {{ sizeGrid.name }}</h1>
            <el-tooltip content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>

        <div class="p-5 bg-white rounded-md">
            <SizeGridInfo :size-grid="sizeGrid" />

            <HelpBlock v-if="showHelp">
                <p><b>Название сетки</b> является обязательным полем.</p>
                <p><b>Измерения</b> — список измерений сетки (например, «Размер», «Полнота»).</p>
                <p><b>Значения</b> — добавляются внутри каждого измерения. Поле «Подпись» можно не заполнять,
                    тогда будет отображаться само значение. Порядок значений меняется перетаскиванием
                    за иконку слева.</p>
            </HelpBlock>
        </div>
    </el-config-provider>
</template>

<script lang="ts" setup>
import {ref, defineProps} from "vue";
import {Head} from '@inertiajs/vue3'
import ru from 'element-plus/dist/locale/ru.mjs'
import SizeGridInfo from './Block/Info.vue'
import HelpBlock from "@Comp/HelpBlock.vue";

const props = defineProps({
    sizeGrid: Object,
    title: {
        type: String,
        default: 'Карточка размерной сетки',
    },
})

const showHelp = ref(false);
</script>
