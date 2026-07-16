<template>
    <div class="content-block-editor">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-medium text-lg">Блоки контента</h2>
            <el-button type="primary" size="small" :loading="loading" @click="createBlock">
                + Добавить блок
            </el-button>
        </div>

        <!-- Список блоков -->
        <div ref="sortableEl" class="space-y-3">
            <ContentBlockItem
                v-for="(element, index) in localBlocks"
                :key="element.id"
                :block="element"
                :index="index"
                :collapsed="collapsedIds.has(element.id)"
                @toggle="toggleCollapse"
                @edit="openEditDialog"
                @delete="confirmDelete"
                @add-widget="openWidgetSelector"
                @remove-widget="confirmRemoveWidget"
                @toggle-active="onToggleActive"
            />
        </div>

        <el-empty v-if="localBlocks.length === 0" description="Нет блоков" class="py-8" />

        <!-- Диалог редактирования блока -->
        <el-dialog
            :model-value="showEditDialog"
            @update:model-value="showEditDialog = $event"
            title="Редактировать блок"
            width="400px"
        >
            <el-form label-position="top">
                <el-form-item label="Название (caption)">
                    <el-input v-model="editCaption" placeholder="Необязательно" />
                </el-form-item>
                <el-form-item label="Секция">
                    <el-select v-model="editSection" placeholder="Необязательно" clearable class="w-full">
                        <el-option
                            v-for="sec in contentStore.sections"
                            :key="sec.value"
                            :label="sec.label"
                            :value="sec.value"
                        />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showEditDialog = false">Отмена</el-button>
                <el-button type="primary" :loading="loading" @click="saveEdit">
                    Сохранить
                </el-button>
            </template>
        </el-dialog>

        <!-- Диалог выбора виджета -->
        <WidgetSelectorDialog
            :visible="showWidgetSelector"
            @close="showWidgetSelector = false"
            @select="onWidgetSelected"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue'
import Sortable from 'sortablejs'
import { useContentBlock } from '@Res/composables/useContentBlock'
import { useContentStore } from '@Res/contentStore'
import ContentBlockItem from './ContentBlockItem.vue'
import WidgetSelectorDialog from './WidgetSelectorDialog.vue'

interface ContentBlockData {
    id: number
    containerType: string
    containerId: number
    widgetInstanceId: number | null
    sort: number | null
    section: string | null
    caption: string | null
    widgetInstance: any
    createdAt: string | null
    updatedAt: string | null
}

const props = defineProps<{
    blocks: ContentBlockData[]
    containerId: number
    containerType: string
}>()

const { loading, createBlock: apiCreateBlock, updateBlock: apiUpdateBlock, deleteBlock: apiDeleteBlock, sortBlock: apiSortBlock, toggleBlock: apiToggleBlock, createWidgetInstance, deleteWidgetInstance } = useContentBlock()
const contentStore = useContentStore()

const localBlocks = ref<ContentBlockData[]>([])
const sortableEl = ref<HTMLElement | null>(null)
const collapsedIds = ref<Set<number>>(new Set())
let sortableInstance: Sortable | null = null

// --- Редактирование ---
const showEditDialog = ref(false)
const editBlockId = ref<number | null>(null)
const editCaption = ref('')
const editSection = ref<string | null>(null)

function openEditDialog(id: number) {
    const block = localBlocks.value.find(b => b.id === id)
    if (!block) return
    editBlockId.value = id
    editCaption.value = block.caption || ''
    editSection.value = block.section || null
    showEditDialog.value = true
}

async function saveEdit() {
    if (editBlockId.value === null) return
    const id = editBlockId.value

    const updated = await apiUpdateBlock(id, {
        caption: editCaption.value || null,
        section: editSection.value || null,
    })

    const block = localBlocks.value.find(b => b.id === id)
    if (block) {
        block.caption = updated.caption
        block.section = updated.section
    }

    showEditDialog.value = false
    editBlockId.value = null
    editCaption.value = ''
    editSection.value = null
}

// --- Виджеты ---
const showWidgetSelector = ref(false)
const selectedBlockId = ref<number | null>(null)

function openWidgetSelector(blockId: number) {
    selectedBlockId.value = blockId
    showWidgetSelector.value = true
}

async function onWidgetSelected(widget: any) {
    showWidgetSelector.value = false
    if (selectedBlockId.value === null) return

    const blockId = selectedBlockId.value
    selectedBlockId.value = null

    const instance = await createWidgetInstance({
        widget_id: widget.id,
        content_block_id: blockId,
    })

    const block = localBlocks.value.find(b => b.id === blockId)
    if (block) {
        block.widgetInstanceId = instance.id
        block.widgetInstance = instance
    }
}

async function confirmRemoveWidget(blockId: number) {
    const block = localBlocks.value.find(b => b.id === blockId)
    if (!block || !block.widgetInstance) return

    await deleteWidgetInstance(block.widgetInstance.id)

    block.widgetInstanceId = null
    block.widgetInstance = null
}

// --- Загрузка виджетов ---
onMounted(() => {
    if (contentStore.widgets.length === 0) {
        contentStore.reload()
    }
})

// --- Переключение active ---
async function onToggleActive(blockId: number, active: boolean) {
    const block = localBlocks.value.find(b => b.id === blockId)
    if (!block) return

    try {
        const updated = await apiToggleBlock(blockId)
        block.active = updated.active
    } catch (e) {
        console.error('Ошибка переключения active:', e)
    }
}

// --- Сортировка и коллапс ---
watch(() => props.blocks, (val) => {
    localBlocks.value = [...val]

    // Все блоки при загрузке — свернуты
    collapsedIds.value = new Set(val.map(b => b.id))

    nextTick(initSortable)
}, { immediate: true })

onMounted(initSortable)

function initSortable() {
    if (!sortableEl.value) return

    if (sortableInstance) {
        sortableInstance.destroy()
    }

    sortableInstance = Sortable.create(sortableEl.value, {
        handle: '.drag-handle',
        animation: 200,
        onEnd: async (evt) => {
            const children = sortableEl.value!.children
            if (!children.length) return

            const newOrder: ContentBlockData[] = []
            for (let i = 0; i < children.length; i++) {
                const id = Number(children[i].getAttribute('data-id'))
                const block = localBlocks.value.find(b => b.id === id)
                if (block) {
                    newOrder.push({ ...block, sort: i + 1 })
                }
            }
            localBlocks.value = newOrder

            const movedId = Number(evt.item.getAttribute('data-id'))
            const movedBlock = newOrder.find(b => b.id === movedId)
            if (movedBlock) {
                await apiSortBlock(movedBlock.id, movedBlock.sort!)
            }
        },
    })
}

function toggleCollapse(id: number) {
    const newSet = new Set(collapsedIds.value)
    if (newSet.has(id)) {
        newSet.delete(id)
    } else {
        newSet.add(id)
    }
    collapsedIds.value = newSet
}

// --- Создание (мгновенное) ---
async function createBlock() {
    const created = await apiCreateBlock({
        container_type: props.containerType,
        container_id: props.containerId,
    })

    localBlocks.value.push({
        ...created,
        sort: localBlocks.value.length + 1,
    })

    // Новый блок остаётся развёрнутым, все остальные сворачиваем
    collapsedIds.value = new Set(localBlocks.value.slice(0, -1).map(b => b.id))

    await nextTick(initSortable)
}

// --- Удаление ---
async function confirmDelete(id: number) {
    await apiDeleteBlock(id)
    localBlocks.value = localBlocks.value.filter(b => b.id !== id)
    collapsedIds.value.delete(id)
    await nextTick(initSortable)
}
</script>

<style scoped>
</style>
