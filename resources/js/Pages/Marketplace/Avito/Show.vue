<template>
    <Head><title>{{ product.name }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">{{ product.name }}</h1>

        <div class="mt-3 p-5 bg-white rounded-md">
            <Info
                :product="product"
                :info="info"
                :categories="categories"
                :has-changes="hasChanges"
                @save="onSave"
                @cancel="onCancel"
                @category-changed="onCategoryChanged"
                @product-changed="onProductChanged"
            />
        </div>
        <div class="mt-3 p-5 bg-white rounded-md">
            <el-tabs v-model="activePanel" class="mt-4">
                <PanelDescription
                    :product-id="product.id"
                    :description="product.description ?? ''"
                />
                <PanelParameters
                    :product-id="product.id"
                    :values="values"
                    :category-id="product.avitoCategoryId"
                />
                <PanelImages :product-id="product.id" :product-catalog-id="product.productId"/>
            </el-tabs>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";
import { route } from "ziggy-js";
import Info from "./Block/Info.vue";
import PanelDescription from "./Panels/Description.vue";
import PanelParameters from "./Panels/Parameters.vue";
import PanelImages from "./Panels/Images.vue";

const props = defineProps({
    product: Object,
    values: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
})

const initialInfo = {
    name: props.product.name ?? '',
    basePrice: props.product.basePrice ?? 0,
    sellPrice: props.product.sellPrice ?? null,
    pricePackage: props.product.pricePackage ?? 0,
    tnVed: props.product.tnVed ?? '',
    okpd2: props.product.okpd2 ?? '',
    country: props.product.country ?? '',
    delivery: !!props.product.delivery,
    inStock: !!props.product.inStock,
    active: !!props.product.active,
    sale: !!props.product.sale,
}

const info = reactive({ ...initialInfo })

const infoFields = ['name', 'basePrice', 'sellPrice', 'pricePackage', 'tnVed', 'okpd2', 'country', 'delivery', 'inStock', 'active', 'sale']

// Значения параметров категории сохраняются отдельно (см. PanelParameters).
const hasChanges = computed(() =>
    infoFields.some(
        (key) => JSON.stringify((info as any)[key]) !== JSON.stringify((initialInfo as any)[key])
    )
)

function onSave() {
    router.post(route('admin.marketplace.avito.product.update', { id: props.product.id }), {
        ...info,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
        },
    })
}

function onCancel() {
    Object.assign(info, JSON.parse(JSON.stringify(initialInfo)))
}

// Категория сохраняется отдельно и сразу — перезагружаем страницу,
// чтобы обновить путь категории и значения параметров.
function onCategoryChanged() {
    router.reload({ preserveState: false, preserveScroll: true })
}

// Связанный товар добавили/удалили — перезагружаем страницу.
function onProductChanged() {
    router.reload({ preserveState: false, preserveScroll: true })
}

// Активная вкладка через get-параметр ?panel=xxx (как в Catalog/Product/Edit).
const activePanel = ref<string | number>('description')

const urlParams = new URLSearchParams(window.location.search)
const initialPanel = urlParams.get('panel')
if (initialPanel) {
    activePanel.value = initialPanel
}

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
