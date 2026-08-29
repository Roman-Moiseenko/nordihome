<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <div class="flex">
        <h1 class="font-medium text-xl">Акция {{ promotion.name }}</h1>
            <el-tooltip  content="Помощь" placement="bottom-start" effect="dark">
                <el-button circle class="ml-2" @click="showHelp = !showHelp">
                    <i class="fa-light fa-lightbulb-on text-orange-500"></i>
                </el-button>
            </el-tooltip>
        </div>
        <div class="p-5 bg-white rounded-md">
            <PromotionInfo :promotion="promotion"/>

            <HelpBlock v-if="showHelp">
                <p>Параметры <b>Начало акции</b> и <b>Конец акции</b> нужны для автоматического запуска и завершения
                    акции.</p>
                <p>В ином случае акцию можно запускать в ручную.</p>
                <p><b>Базовая скидка</b> используется для автоматического расчета стоимости товара при его
                    добавлении в
                    акцию. Далее, для каждого товара из акции можно вручную задать цену.</p>
                <p><b>Метка на карточке</b> поля для редактирования информационной плашки в списке товаров и в карточке
                </p>
            </HelpBlock>
        </div>

        <el-tabs>

            <PanelProducts :promotion-id="promotion.id" />
            <PanelBlocks :blocks="blocks || []" :promotion-id="promotion.id"/>
        </el-tabs>

    </el-config-provider>


</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3"
import ru from 'element-plus/dist/locale/ru.mjs'
import { ref} from "vue";

import PromotionInfo from "./Block/Info.vue"
import HelpBlock from "@Comp/HelpBlock.vue";
import PanelProducts from "./Panels/Products.vue";
import PanelBlocks from "./Panels/Blocks.vue";

const props = defineProps({
    promotion: Object,
    title: {
        type: String,
        default: 'Карточка акции',
    },
    blocks: Array,
})

const showHelp = ref(false);

</script>
