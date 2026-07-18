<template>
    <!-- Если товар уже выбран — показываем превью -->
    <div v-if="productData" class="selected-product-wrapper">
        <div class="selected-product-preview">
            <!-- Изображение товара -->
            <div class="product-preview-image">
                <el-image
                    v-if="productData.image_src"
                    :src="productData.image_src"
                    fit="cover"
                    class="product-thumb-img"
                />
                <div v-else class="product-no-image">
                    <el-icon :size="32"><Box /></el-icon>
                </div>
            </div>
            <!-- Информация о товаре -->
            <div class="product-preview-info">
                <div class="product-preview-name">{{ productData.name }}</div>
                <div class="product-preview-code">{{ productData.code }}</div>
                <div v-if="productData.price" class="product-preview-price">
                    {{ formatPrice(productData.price) }}
                </div>
            </div>
            <!-- Действия -->
            <div class="product-preview-actions">
                <el-button size="small" type="primary" @click="openSearch">
                    Выбрать другой
                </el-button>
                <el-button size="small" type="danger" plain @click="removeProduct">
                    Удалить
                </el-button>
            </div>
        </div>
    </div>

    <!-- Если товара нет — кнопка "Выбрать товар" -->
    <div v-else class="empty-product-wrapper">
        <el-button type="primary" @click="openSearch" class="select-product-btn">
            <el-icon class="mr-1"><Plus /></el-icon>
            Выбрать товар
        </el-button>
    </div>

    <!-- Диалог поиска товара -->
    <el-dialog
        v-model="searchDialogVisible"
        title="Выбор товара"
        width="600px"
        :close-on-click-modal="false"
        class="product-picker-dialog"
    >
        <div class="product-search-layout">
            <!-- Поисковое поле -->
            <div class="search-field">
                <el-input
                    v-model="searchQuery"
                    placeholder="Введите артикул или название товара..."
                    clearable
                    @input="onSearchInput"
                    ref="searchInputRef"
                >
                    <template #prefix>
                        <el-icon><Search /></el-icon>
                    </template>
                </el-input>
            </div>

            <!-- Результаты поиска -->
            <div class="search-results" v-loading="searchLoading">
                <div
                    v-for="item in searchResults"
                    :key="item.id"
                    class="search-result-item"
                    :class="{ selected: selectedProductId === item.id }"
                    @click="selectProduct(item)"
                >
                    <div class="result-item-image">
                        <el-image
                            v-if="item.image_src"
                            :src="item.image_src"
                            fit="cover"
                            class="result-thumb-img"
                        />
                        <div v-else class="result-no-image">
                            <el-icon :size="24"><Box /></el-icon>
                        </div>
                    </div>
                    <div class="result-item-info">
                        <div class="result-item-name">{{ item.name }}</div>
                        <div class="result-item-code">{{ item.code }}</div>
                        <div v-if="item.price" class="result-item-price">
                            {{ formatPrice(item.price) }}
                        </div>
                    </div>
                    <div class="result-item-check">
                        <el-icon v-if="selectedProductId === item.id" color="#409eff" :size="20">
                            <Check />
                        </el-icon>
                    </div>
                </div>

                <el-empty v-if="!searchLoading && searchQuery && searchResults.length === 0" description="Товары не найдены" />
            </div>
        </div>

        <template #footer>
            <el-button @click="searchDialogVisible = false">Отмена</el-button>
            <el-button type="primary" :disabled="!selectedProductId" @click="confirmSelection">
                Выбрать
            </el-button>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, nextTick } from 'vue'
import { Plus, Search, Check, Box } from '@element-plus/icons-vue'
import axios from 'axios'
// @ts-ignore
import { route } from 'ziggy-js'

export interface ProductPickerData {
    id: number | null
    name: string | null
    code?: string | null
    url?: string | null
    short?: string | null
    price?: number | null
    image_src?: string | null
    image_alt?: string | null
    image_next_src?: string | null
    image_next_alt?: string | null
}

interface SearchResultItem {
    id: number
    name: string
    code: string
    url: string | null
    short: string | null
    price: number | null
    image_src: string | null
    image_alt: string | null
    image_next_src: string | null
    image_next_alt: string | null
}

const props = defineProps<{
    modelValue: ProductPickerData | null
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: ProductPickerData | null): void
}>()

// Состояние
const searchDialogVisible = ref(false)
const searchQuery = ref('')
const searchLoading = ref(false)
const searchResults = ref<SearchResultItem[]>([])
const selectedProductId = ref<number | null>(null)
const searchInputRef = ref<any>(null)

// Текущий выбранный товар (из modelValue)
const productData = computed<ProductPickerData | null>(() => {
    if (!props.modelValue || !props.modelValue.id) return null

    // Если modelValue содержит только id (без данных), подгружаем через search
    if (!props.modelValue.name && props.modelValue.id) {
        loadProductById(props.modelValue.id)
    }

    return props.modelValue
})

/**
 * Загружает данные о товаре по ID через search_add endpoint
 */
async function loadProductById(id: number) {
    try {
        const response = await axios.post(
            route('admin.catalog.product.search-add'),
            { search: String(id) }
        )
        console.log(response)
        const data = response.data
        // search_add возвращает массив — ищем товар по id
        if (Array.isArray(data) && data.length > 0) {
            const found = data.find((item: any) => item.id === id)
            if (found) {
                emit('update:modelValue', normalizeProductData(found))
            }
        }
    } catch (e) {
        console.error('Ошибка загрузки товара по ID:', e)
    }
}

/**
 * Нормализует данные из search_add в формат ProductPickerData
 */
function normalizeProductData(item: any): ProductPickerData {
    return {
        id: item.id,
        name: item.name,
        code: item.code,
        url: item.url,
        short: item.short,
        price: item.price,
        image_src: item.image_src,
        image_alt: item.image_alt,
        image_next_src: item.image_next_src,
        image_next_alt: item.image_next_alt,
    }
}

/**
 * Дебаунс для поиска
 */
let searchTimer: ReturnType<typeof setTimeout> | null = null

function onSearchInput() {
    if (searchTimer) clearTimeout(searchTimer)

    if (!searchQuery.value || searchQuery.value.length < 2) {
        searchResults.value = []
        return
    }

    searchTimer = setTimeout(() => {
        performSearch()
    }, 300)
}

async function performSearch() {
    if (!searchQuery.value) return

    searchLoading.value = true
    try {
        const response = await axios.post(
            route('admin.catalog.product.search-add'),
            { search: searchQuery.value }
        )
        searchResults.value = Array.isArray(response.data) ? response.data : []
    } catch (e) {
        console.error('Ошибка поиска товаров:', e)
        searchResults.value = []
    } finally {
        searchLoading.value = false
    }
}

function openSearch() {
    selectedProductId.value = props.modelValue?.id ?? null
    searchQuery.value = ''
    searchResults.value = []
    searchDialogVisible.value = true

    // Фокус на поле поиска при открытии
    nextTick(() => {
        searchInputRef.value?.focus()
    })
}

function selectProduct(item: SearchResultItem) {
    // toggle: если кликнуть на уже выбранный — снимаем выбор
    if (selectedProductId.value === item.id) {
        selectedProductId.value = null
    } else {
        selectedProductId.value = item.id
    }
}

function confirmSelection() {
    if (!selectedProductId.value) return

    const selected = searchResults.value.find(r => r.id === selectedProductId.value)
    if (selected) {
        emit('update:modelValue', normalizeProductData(selected))
    }

    searchDialogVisible.value = false
}

function removeProduct() {
    emit('update:modelValue', null)
}

function formatPrice(price: number): string {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2,
    }).format(price)
}
</script>

<style scoped>
/* Выбранный товар */
.selected-product-wrapper {
    margin-bottom: 12px;
}

.selected-product-preview {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.product-preview-image {
    width: 80px;
    height: 80px;
    min-width: 80px;
    border-radius: 6px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #d1d5db;
}

.product-preview-info {
    flex: 1;
    min-width: 0;
}

.product-preview-name {
    font-weight: 500;
    font-size: 14px;
    color: #1f2937;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-preview-code {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
}

.product-preview-price {
    font-size: 14px;
    font-weight: 600;
    color: #059669;
    margin-top: 4px;
}

.product-preview-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 120px;
}

/* Пустое состояние */
.empty-product-wrapper {
    margin-bottom: 12px;
}

.select-product-btn {
    width: 100%;
}

/* Диалог поиска */
.product-picker-dialog :deep(.el-dialog__body) {
    padding: 16px;
}

.product-search-layout {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.search-field {
    position: sticky;
    top: 0;
    z-index: 1;
}

.search-results {
    max-height: 400px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 60px;
}

.search-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s;
}

.search-result-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.search-result-item.selected {
    background: #eff6ff;
    border-color: #93c5fd;
    box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.2);
}

.result-item-image {
    width: 60px;
    height: 60px;
    min-width: 60px;
    border-radius: 6px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.result-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.result-no-image {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #d1d5db;
}

.result-item-info {
    flex: 1;
    min-width: 0;
}

.result-item-name {
    font-weight: 500;
    font-size: 13px;
    color: #1f2937;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.result-item-code {
    font-size: 12px;
    color: #6b7280;
    margin-top: 2px;
}

.result-item-price {
    font-size: 13px;
    font-weight: 600;
    color: #059669;
    margin-top: 2px;
}

.result-item-check {
    width: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
