<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl my-2">Редактирование товара {{ product.name }}</h1>
        <div class="mt-2 p-3 bg-white rounded-md">
            <el-radio-group v-model="tabPosition" style="margin-bottom: 30px">
                <el-radio-button value="top">top</el-radio-button>
                <el-radio-button value="right">right</el-radio-button>
                <el-radio-button value="bottom">bottom</el-radio-button>
                <el-radio-button value="left">left</el-radio-button>
            </el-radio-group>

            <el-tabs :tab-position="tabPosition">

                <PanelCommon :product="product"
                             :errors="errors"
                             :categories="categories"
                             :brands="brands"
                             :country="country"
                             :vat="vat"
                             :measuring="measuring"
                             :markingType="markingType"
                />
                <PanelDescription :product="product"
                                  :errors="errors"
                                  :tags="tags"
                                  :series="series"
                />
                <PanelDimensions :product="product"
                                 :errors="errors"
                                 :dimensions="dimensions"
                                 :complexities="complexities"
                />
                <PanelImage :product="product"
                            :errors="errors"
                />
                <PanelVideo :product="product"
                            :errors="errors"
                />
                <PanelAttribute :product="product"
                                :errors="errors"
                />
                <PanelManagement :product="product"
                                 :errors="errors"
                                 :frequencies="frequencies"
                />
                <PanelModification :product="product"
                                   :errors="errors"
                />
                <PanelEquivalent :product="product"
                                 :errors="errors"
                                 :equivalents="equivalents"
                />
                <PanelRelated :product="product"
                              :errors="errors"
                />
                <PanelBonus :product="product"
                            :errors="errors"
                />
                <PanelComposite :product="product"
                                :errors="errors"
                />
            </el-tabs>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive, ref} from "vue";
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
    product: Object,
    errors: Object,
    categories: Array,
    brands: Array,
    country: Array,
    vat: Array,
    tags: Array,
    series: Array,
    measuring: Array,
    markingType: Array,
    distributors: Array,
    dimensions: Array,
    frequencies: Array,
    equivalents: Array,
    complexities: Array,
    title: {
        type: String,
        default: 'Редактирование товара',
    },
})
const tabPosition = ref<TabsInstance['tabPosition']>('left')
</script>

<style scoped>

</style>
