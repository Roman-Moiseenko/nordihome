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
                :default-expanded-keys="expandedKeys"
                @check="onCheck"
            />
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
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

// Ключи узлов, которые нужно раскрыть при загрузке:
// все родители выбранных категорий, чтобы выбранные элементы были видны.
const expandedKeys = computed(() => {
    const parentOf = new Map()

    const walk = (nodes, parentId = null) => {
        for (const node of nodes ?? []) {
            if (parentId !== null) {
                parentOf.set(node.id, parentId)
            }
            if (node.children && node.children.length) {
                walk(node.children, node.id)
            }
        }
    }
    walk(catalog.categoriesTree)

    const selected = new Set((props.rule.categoryIds ?? []).map(Number))
    const expand = new Set()

    for (const id of selected) {
        let current = id
        while (parentOf.has(current)) {
            const parent = parentOf.get(current)
            expand.add(parent)
            current = parent
        }
    }

    return [...expand]
})

function onCheck(node, info) {
    const ids = [...info.checkedKeys].map(Number)
    const clickedKey = node.id
    const isCheckedNow = info.checkedKeys.some((key) => String(key) === String(clickedKey))

    router.post(route('admin.accounting.pricing-rule.categories.sync', {id: props.rule.id}), {
        category_ids: ids,
    }, {
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            // При ошибке сохраняем прежнее (ненажатое) состояние галочки
            treeRef.value?.setChecked(clickedKey, !isCheckedNow, false)
        },
        onFinish: () => {
            // Страховка: приводим нажатую галочку к серверному состоянию
            const serverIds = props.rule.categoryIds ?? []
            const shouldBeChecked = serverIds.some((id) => String(id) === String(clickedKey))
            treeRef.value?.setChecked(clickedKey, shouldBeChecked, false)
        },
    })
}
</script>
