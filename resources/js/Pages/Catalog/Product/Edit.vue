<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl my-2">{{ header }}</h1>
        <div class="mt-2 p-3 bg-white rounded-md">
            <el-radio-group v-model="tabPosition" style="margin-bottom: 30px">
                <el-radio-button value="top">top</el-radio-button>
                <el-radio-button value="right">right</el-radio-button>
                <el-radio-button value="bottom">bottom</el-radio-button>
                <el-radio-button value="left">left</el-radio-button>
            </el-radio-group>

            <el-tabs :tab-position="tabPosition" v-model="activePanel">

                <PanelCommon :product-id="productId"
                             :active="activePanel === 'common'"
                             @update:header="onHeader"
                />
                <PanelDescription :product-id="productId"
                                  :active="activePanel === 'description'"
                />
                <PanelDimensions :product-id="productId"
                                 :active="activePanel === 'dimensions'"
                />
                <PanelImage :product-id="productId"
                            :active="activePanel === 'image'"
                />
                <PanelVideo :product-id="productId"
                            :active="activePanel === 'video'"
                />
                <PanelAttribute :product-id="productId"
                                :active="activePanel === 'attribute'"
                />
                <PanelManagement :product-id="productId"
                                 :active="activePanel === 'management'"
                />
                <PanelModification :product-id="productId"
                                   :active="activePanel === 'modification'"
                />
                <PanelEquivalent :product-id="productId"
                                 :active="activePanel === 'equivalent'"
                />
                <PanelRelated :product-id="productId"
                              :active="activePanel === 'related'"
                />
                <PanelBonus :product-id="productId"
                            :active="activePanel === 'bonus'"
                />
                <PanelComposite :product-id="productId"
                                :active="activePanel === 'composite'"
                />
            </el-tabs>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive, ref, watch} from "vue";
import type {TabsInstance} from 'element-plus'
//Панели
import PanelCommon from './Panels/Common.vue'
import PanelDescription from './Panels/Description.vue'
import PanelDimensions from './Panels/Dimensions.vue'
import PanelImage from './Panels/Image.vue'
import PanelVideo from './Panels/Video.vue'
import PanelAttribute from './Panels/Attribute.vue'
import PanelManagement from './Panels/Management.vue'
import PanelModification from './Panels/Modification.vue'
import PanelEquivalent from './Panels/Equivalent.vue'
import PanelRelated from './Panels/Related.vue'
import PanelBonus from './Panels/Bonus.vue'
import PanelComposite from './Panels/Composite.vue'


const props = defineProps({
    productId: Number,

    title: {
        type: String,
        default: 'Редактирование товара',
    },
})
const tabPosition = ref<TabsInstance['tabPosition']>('left')

// Заголовок страницы. Пока панель «Общие параметры» не загрузила данные,
// показываем нейтральный заголовок; после загрузки — «Название (Артикул)».
const header = ref('Редактирование товара')

function onHeader({ name, code }) {
    header.value = `${name} (${code})`
}

// Активная панель. По умолчанию — первая (Общие параметры).
// Если страница открыта с ?panel=xxx, эта панель становится активной сразу.
const activePanel = ref<string | number>('common')

const urlParams = new URLSearchParams(window.location.search)
const initialPanel = urlParams.get('panel')
if (initialPanel) {
    activePanel.value = initialPanel
}

// При переключении панели пишем get-параметр ?panel=xxx в URL
// без перезагрузки страницы (сохраняем history.state Inertia).
watch(activePanel, (value) => {
    const url = new URL(window.location.href)
    if (value && String(value) !== '') {
        url.searchParams.set('panel', String(value))
    } else {
        url.searchParams.delete('panel')
    }
    window.history.replaceState(window.history.state, '', url.toString())
})
</script>

<style scoped>

</style>
