<template>
    <!-- Если сущность уже выбрана — показываем превью -->
    <div v-if="groupData" class="selected-group-wrapper">
        <div class="selected-group-preview">
            <div class="group-preview-icon">
                <el-icon :size="28"><CollectionTag /></el-icon>
            </div>
            <div class="group-preview-info">
                <div class="group-preview-type">{{ typeLabel }}</div>
                <div class="group-preview-title">«{{ groupData.title || groupData.entity_id }}»</div>
            </div>
            <div class="group-preview-actions">
                <el-button size="small" type="primary" @click="openDialog">
                    Заменить
                </el-button>
                <el-button size="small" type="danger" plain @click="removeGroup">
                    Удалить
                </el-button>
            </div>
        </div>
    </div>

    <!-- Если сущности нет — кнопка "Выбрать группу товаров" -->
    <div v-else class="empty-group-wrapper">
        <el-button type="primary" @click="openDialog" class="select-group-btn">
            <el-icon class="mr-1"><Plus /></el-icon>
            Выбрать группу товаров
        </el-button>
    </div>

    <!-- Диалог выбора сущности -->
    <el-dialog
        v-model="dialogVisible"
        title="Выбор группы товаров"
        width="640px"
        :close-on-click-modal="false"
        class="product-group-picker-dialog"
    >
        <div v-loading="!contentStore.loaded" class="group-search-layout">
            <!-- Радиокнопки типов сущностей (с бэкенда) -->
            <div class="type-selector">
                <el-radio-group v-model="selectedType" size="small" @change="onTypeChange">
                    <el-radio-button
                        v-for="(label, type) in contentStore.types"
                        :key="type"
                        :value="type"
                    >
                        {{ label }}
                    </el-radio-button>
                </el-radio-group>
            </div>

            <!-- Поиск по названию сущности -->
            <div class="search-field">
                <el-input
                    v-model="searchQuery"
                    placeholder="Поиск по названию..."
                    clearable
                >
                    <template #prefix>
                        <el-icon><Search /></el-icon>
                    </template>
                </el-input>
            </div>

            <!-- Список сущностей выбранного типа -->
            <div class="group-results">
                <div
                    v-for="item in filteredEntities"
                    :key="item.id"
                    class="group-result-item"
                    :class="{ selected: selectedEntityId === item.id }"
                    @click="selectEntity(item)"
                >
                    <div class="result-item-name"><el-tag v-if="item.published == false" type="danger" round effect="dark">-</el-tag> {{ item.name }}</div>
                    <div class="result-item-check">
                        <el-icon v-if="selectedEntityId === item.id" color="#409eff" :size="20">
                            <Check />
                        </el-icon>
                    </div>
                </div>

                <el-empty
                    v-if="filteredEntities.length === 0"
                    :description="searchQuery ? 'Ничего не найдено' : 'Список пуст'"
                />
            </div>
        </div>

        <template #footer>
            <el-button @click="dialogVisible = false">Отмена</el-button>
            <el-button type="primary" :disabled="!selectedEntityId" @click="confirmSelection">
                Выбрать
            </el-button>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Plus, Search, Check, CollectionTag } from '@element-plus/icons-vue'
import axios from 'axios'
// @ts-ignore
import { route } from 'ziggy-js'
import { useCatalogStore } from '@Res/catalogStore'
import {useContentStore} from "@Res/contentStore";

export interface ProductGroupData {
    entity_type: string | null
    entity_id: number | null
    title: string | null
    limit?: number | null
}

interface EntityItem {
    id: number
    name: string,
    published: boolean,
}

const props = defineProps<{
    modelValue: ProductGroupData | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: ProductGroupData | null): void
}>()

const catalogStore = useCatalogStore()
const contentStore = useContentStore()

// --- Состояние диалога ---
const dialogVisible = ref(false)
const selectedType = ref('category')
const searchQuery = ref('')
const selectedEntityId = ref<number | null>(null)

const groupData = computed<ProductGroupData | null>(() => {
    if (!props.modelValue || !props.modelValue.entity_id) return null
    return props.modelValue
})

const typeLabel = computed(() => {
    const type = groupData.value?.entity_type
    if (!type) return ''
    return contentStore.types[type] ?? type
})

/**
 * Источник данных для выбранного типа из catalogStore.
 * Все источники нормализуются к { id, name }.
 */
function sourceFor(type: string): EntityItem[] {
    const normalize = (list: any[]): EntityItem[] =>
        (list || []).map((item) => ({ id: item.id, name: item.name, published: item.published }))

    switch (type) {
        case 'room':
            return normalize(catalogStore.rooms)
        case 'category':
            return normalize(catalogStore.categories)
        case 'group':
            return normalize(catalogStore.groups)
        case 'promotion':
            return normalize(catalogStore.promotions)
        case 'series':
            return normalize(catalogStore.series)
        default:
            return []
    }
}

const filteredEntities = computed<EntityItem[]>(() => {
    const q = searchQuery.value.trim().toLowerCase()
    const list = sourceFor(selectedType.value)
    if (!q) return list
    return list.filter((item) => (item.name || '').toLowerCase().includes(q))
})

function onTypeChange() {
    searchQuery.value = ''
    selectedEntityId.value = null
}

function selectEntity(item: EntityItem) {
    if (selectedEntityId.value === item.id) {
        selectedEntityId.value = null
    } else {
        selectedEntityId.value = item.id
    }
}

function openDialog() {
  //  loadTypes()
    if (!catalogStore.loaded) {
        catalogStore.reload()
    }

    selectedType.value = props.modelValue?.entity_type || Object.keys(types.value)[0] || 'category'
    selectedEntityId.value = props.modelValue?.entity_id ?? null
    searchQuery.value = ''
    dialogVisible.value = true
}

function confirmSelection() {
    if (!selectedEntityId.value) return

    const entity = sourceFor(selectedType.value).find((item) => item.id === selectedEntityId.value)
    if (!entity) return

    emit('update:modelValue', {
        entity_type: selectedType.value,
        entity_id: entity.id,
        title: entity.name,
        limit: props.modelValue?.limit ?? null,
    })

    dialogVisible.value = false
}

function removeGroup() {
    emit('update:modelValue', null)
}
</script>

<style scoped>
/* Выбранная сущность */
.selected-group-wrapper {
    margin-bottom: 12px;
}

.selected-group-preview {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.group-preview-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 6px;
    background: #eff6ff;
    color: #409eff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.group-preview-info {
    flex: 1;
    min-width: 0;
}

.group-preview-type {
    font-size: 12px;
    color: #6b7280;
}

.group-preview-title {
    font-weight: 500;
    font-size: 14px;
    color: #1f2937;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.group-preview-actions {
    display: flex;
    gap: 6px;
}

/* Пустое состояние */
.empty-group-wrapper {
    margin-bottom: 12px;
}

.select-group-btn {
    width: 100%;
}

/* Диалог */
.product-group-picker-dialog :deep(.el-dialog__body) {
    padding: 16px;
}

.group-search-layout {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.type-selector {
    display: flex;
    flex-wrap: wrap;
}

.search-field {
    position: sticky;
    top: 0;
    z-index: 1;
}

.group-results {
    max-height: 320px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 60px;
}

.group-result-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
}

.group-result-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.group-result-item.selected {
    background: #eff6ff;
    border-color: #93c5fd;
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.2);
}

.result-item-name {
    font-weight: 500;
    font-size: 13px;
    color: #1f2937;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.result-item-check {
    width: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
