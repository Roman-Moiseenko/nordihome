<template>
    <div class="content-block-editor">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-medium text-lg">Блоки контента</h2>
            <el-button type="primary" size="small" @click="showCreateDialog = true">
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
                @delete="confirmDelete"
            />
        </div>

        <el-empty v-if="localBlocks.length === 0" description="Нет блоков" class="py-8" />

        <el-dialog
            :model-value="showCreateDialog"
            @update:model-value="showCreateDialog = $event"
            title="Создать блок"
            width="400px"
        >
            <el-form label-position="top">
                <el-form-item label="Название (caption)">
                    <el-input v-model="newBlockCaption" placeholder="Необязательно" />
                </el-form-item>
                <el-form-item label="Секция">
                    <el-select v-model="newBlockSection" placeholder="Необязательно" clearable class="w-full">
                        <el-option label="header" value="header" />
                        <el-option label="body" value="body" />
                        <el-option label="sidebar" value="sidebar" />
                        <el-option label="footer" value="footer" />
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showCreateDialog = false">Отмена</el-button>
                <el-button type="primary" :loading="loading" @click="createBlock">
                    Создать
                </el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, nextTick } from 'vue'
import Sortable from 'sortablejs'
import { useContentBlock } from '@Res/composables/useContentBlock'
import ContentBlockItem from './ContentBlockItem.vue'

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

const { loading, createBlock: apiCreateBlock, deleteBlock: apiDeleteBlock, sortBlock: apiSortBlock } = useContentBlock()

const localBlocks = ref<ContentBlockData[]>([])
const sortableEl = ref<HTMLElement | null>(null)
const collapsedIds = ref<Set<number>>(new Set())
let sortableInstance: Sortable | null = null

watch(() => props.blocks, (val) => {
    localBlocks.value = [...val]

    if (val.length > 0) {
        collapsedIds.value = new Set(val.slice(0, -1).map(b => b.id))
    }

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
        onEnd: async () => {
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

            const first = localBlocks.value[0]
            if (first) {
                await apiSortBlock(first.id, first.sort!)
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

// --- Создание ---
const showCreateDialog = ref(false)
const newBlockCaption = ref('')
const newBlockSection = ref('')

async function createBlock() {
    const created = await apiCreateBlock({
        container_type: props.containerType,
        container_id: props.containerId,
        caption: newBlockCaption.value || null,
        section: newBlockSection.value || null,
    })

    localBlocks.value.push({
        ...created,
        sort: localBlocks.value.length + 1,
    })

    collapsedIds.value = new Set(localBlocks.value.slice(0, -1).map(b => b.id))

    showCreateDialog.value = false
    newBlockCaption.value = ''
    newBlockSection.value = ''

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
