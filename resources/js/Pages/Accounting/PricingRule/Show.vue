<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Правило «{{ rule.name }}»</h1>

        <div class="p-5 bg-white rounded-md mt-3">
            <PricingRuleInfo :rule="rule" />
        </div>

        <div class="p-5 bg-white rounded-md mt-3">
            <h2 class="font-medium text-lg mb-3">Категории товаров</h2>
            <el-tree
                class="!bg-green-50"

                :data="catalog.categoriesTree"
                show-checkbox
                check-strictly
                node-key="id"
                :props="defaultProps"
                :default-checked-keys="[...rule.categoryIds]"
                @check="onCheck"
            />
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import PricingRuleInfo from "./Block/Info.vue";
import {useCatalogStore} from "@Res/catalogStore";

const props = defineProps({
    rule: Object,
    title: {
        type: String,
        default: 'Карточка правила расчета цены',
    },
})

const catalog = useCatalogStore()

const defaultProps = {
    children: 'children',
    label: 'name',
}

function onCheck(node, info) {
    const ids = [...info.checkedKeys].map(Number)

    router.post(route('admin.accounting.pricing-rule.categories.sync', {id: props.rule.id}), {
        category_ids: ids,
    }, {
        preserveScroll: true,
    })
}
</script>
