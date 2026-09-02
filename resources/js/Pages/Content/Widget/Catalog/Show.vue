<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Каталог {{ widget.name }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <CatalogInfo :widget="widget" :templates="templates"/>
        </div>

        <div class="mt-3">
            <el-button type="primary" @click="openAdd">
                <el-icon class="mr-1"><Plus /></el-icon>
                Добавить
            </el-button>
        </div>

        <div class="mt-3 p-3 bg-white rounded-lg ">
            <CatalogItems :items="widget.items" @edit="openEdit" />
        </div>

        <!-- Диалог добавления/редактирования элемента каталога -->
        <el-dialog
            v-model="dialogVisible"
            :title="dialogTitle"
            width="640px"
            :close-on-click-modal="false"
            class="catalog-item-picker-dialog"
        >
            <div v-loading="!contentStore.loaded" class="item-picker-layout">
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
                <div class="item-results">
                    <div
                        v-for="item in filteredEntities"
                        :key="item.id"
                        class="item-result-item"
                        :class="{ selected: selectedEntityId === item.id }"
                        @click="selectEntity(item)"
                    >
                        <div class="result-item-name">
                            <el-tag v-if="item.published == false" type="danger" round effect="dark">-</el-tag>
                            {{ item.name }}
                        </div>
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

                <!-- Необязательные поля -->
                <el-form label-width="auto" class="item-fields">
                    <el-form-item label="Заголовок">
                        <el-input v-model="form.caption" placeholder="Необязательно"/>
                    </el-form-item>
                    <el-form-item label="Описание">
                        <el-input v-model="form.description" type="textarea" :rows="4" placeholder="Необязательно"/>
                    </el-form-item>
                </el-form>
            </div>

            <template #footer>
                <el-button @click="dialogVisible = false">Отмена</el-button>
                <el-button type="primary" :disabled="!selectedEntityId" @click="save">
                    Сохранить
                </el-button>
            </template>
        </el-dialog>
    </el-config-provider>
</template>

<script setup lang="ts">
import {Head, router} from "@inertiajs/vue3";
import {computed, reactive, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Plus, Search, Check} from '@element-plus/icons-vue'
import {useCatalogStore} from "@Res/catalogStore";
import {useContentStore} from "@Res/contentStore";

import CatalogInfo from './Block/Info.vue'
import CatalogItems from './Block/Items.vue'

const props = defineProps({
    widget: Object,
    templates: Array,
    title: {
        type: String,
        default: 'Карточка каталога',
    },
})

const catalogStore = useCatalogStore()
const contentStore = useContentStore()

const form = reactive({
    id: null,
    modelId: null,
    modelType: null,
    caption: null,
    description: null,
})

// --- Состояние диалога ---
const dialogVisible = ref(false)
const dialogTitle = ref('Добавить элемент')
const selectedType = ref('category')
const searchQuery = ref('')
const selectedEntityId = ref(null)

/**
 * Источник данных для выбранного типа из catalogStore.
 * Все источники нормализуются к { id, name, published }.
 */
function sourceFor(type) {
    const normalize = (list) => (list || []).map((item) => ({ id: item.id, name: item.name, published: item.published }))

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

const filteredEntities = computed(() => {
    const q = searchQuery.value.trim().toLowerCase()
    const list = sourceFor(selectedType.value)
    if (!q) return list
    return list.filter((item) => (item.name || '').toLowerCase().includes(q))
})

function onTypeChange() {
    searchQuery.value = ''
    selectedEntityId.value = null
}

function selectEntity(item) {
    selectedEntityId.value = selectedEntityId.value === item.id ? null : item.id
}

function openDialog() {
    if (!catalogStore.loaded) {
        catalogStore.reload()
    }

    selectedType.value = form.modelType || Object.keys(contentStore.types)[0] || 'category'
    selectedEntityId.value = form.modelId ?? null
    searchQuery.value = ''
    dialogVisible.value = true
}

function openAdd() {
    form.id = null
    form.modelId = null
    form.modelType = null
    form.caption = null
    form.description = null
    dialogTitle.value = 'Добавить элемент'
    openDialog()
}

function openEdit(row) {
    form.id = row.id
    form.modelId = row.model_id
    form.modelType = row.model_type
    form.caption = row.caption
    form.description = row.description
    dialogTitle.value = 'Редактировать элемент'
    openDialog()
}

function save() {
    if (!selectedEntityId.value) return

    form.modelId = selectedEntityId.value
    form.modelType = selectedType.value

    if (form.id) {
        onSetItem()
    } else {
        onAddItem()
    }
}

function onAddItem(val) {
    router.visit(route('admin.content.widget.catalog.add-item', {widget: props.widget.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogVisible.value = false
        }
    })
}

function onSetItem() {
    router.visit(route('admin.content.widget.catalog.set-item', {item: form.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            dialogVisible.value = false
        }
    })
}
</script>

<style scoped>
.catalog-item-picker-dialog :deep(.el-dialog__body) {
    padding: 16px;
}

.item-picker-layout {
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

.item-results {
    max-height: 320px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 60px;
}

.item-result-item {
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

.item-result-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.item-result-item.selected {
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

.item-fields {
    border-top: 1px solid #e5e7eb;
    padding-top: 12px;
    margin-top: 4px;
}
</style>
