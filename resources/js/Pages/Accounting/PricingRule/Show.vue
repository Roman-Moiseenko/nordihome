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
                ref="treeRef"
                class="!bg-green-50"
                style="max-width: 600px"
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
import {ref} from "vue";
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
const treeRef = ref()

const defaultProps = {
    children: 'children',
    label: 'name',
}

function onCheck(node, info) {
    const ids = [...info.checkedKeys].map(Number)
    const clickedKey = node.id
    const isChecked = info.checkedKeys.some((key) => String(key) === String(clickedKey))

    router.post(route('admin.accounting.pricing-rule.categories.sync', {id: props.rule.id}), {
        category_ids: ids,
    }, {
        preserveScroll: true,
        onError: () => {
            // Вернуть галочку в прежнее (ненажатое) состояние при ошибке сохранения
            treeRef.value?.setChecked(clickedKey, !isChecked, false)
        },
    })
}
</script>
