<template>
    <div class="array-object-field">
        <div
            v-for="(item, itemIdx) in items"
            :key="itemIdx"
            class="array-object-item border rounded p-3 mb-2"
        >
            <!-- image/product — без сворачивания -->
            <template v-if="field.format === 'image' || field.format === 'product'">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Элемент #{{ itemIdx + 1 }}</span>
                    <el-button
                        size="small"
                        type="danger"
                        text
                        @click="removeItem(itemIdx)"
                    >
                        Удалить
                    </el-button>
                </div>
                <div v-if="field.format === 'image'" class="image-object-field image-item-layout">
                    <div class="image-item-picker">
                        <ImagePicker
                            :model-value="item || null"
                            @update:model-value="(val) => onImageChange(itemIdx, val)"
                        />
                    </div>
                    <div
                        v-if="imageExtraFieldInstances(itemIdx).length > 0"
                        class="image-item-extra"
                    >
                        <WidgetFieldRenderer
                            :fields="imageExtraFieldInstances(itemIdx)"
                            :disabled="disabled"
                            :showSaveButton="false"
                            @save="(vals) => onItemSave(itemIdx, vals)"
                        />
                    </div>
                </div>
                <div v-else class="product-object-field">
                    <ProductPicker
                        :model-value="item || null"
                        @update:model-value="(val) => onProductChange(itemIdx, val)"
                    />
                </div>
            </template>
            <!-- обычный объект — сворачиваемый -->
            <template v-else>
                <div class="composite-field-wrapper border rounded-lg bg-white shadow-sm w-full">
                    <div
                        class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
                        @click="toggleCollapse(itemIdx)"
                    >
                        <el-icon class="text-gray-400" :class="{ 'rotate-90': isCollapsed(itemIdx) }">
                            <i :class="isCollapsed(itemIdx) ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
                        </el-icon>
                        <span class="text-sm font-medium text-gray-600">Элемент #{{ itemIdx + 1 }}</span>
                        <div class="ml-auto flex items-center gap-2" @click.stop>
                            <el-button
                                size="small"
                                type="danger"
                                text
                                @click="removeItem(itemIdx)"
                            >
                                Удалить
                            </el-button>
                        </div>
                    </div>
                    <div v-show="!isCollapsed(itemIdx)" class="border-t px-3 py-3">
                        <WidgetFieldRenderer
                            :fields="nestedFieldInstances(itemIdx)"
                            :disabled="disabled"
                            :showSaveButton="false"
                            @save="(vals) => onItemSave(itemIdx, vals)"
                        />
                    </div>
                </div>
            </template>
        </div>
        <el-button
            v-if="!disabled"
            size="small"
            type="primary"
            plain
            @click="addItem"
        >
            + Добавить элемент
        </el-button>
    </div>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import ImagePicker from './ImagePicker.vue'
import ProductPicker from './ProductPicker.vue'
import WidgetFieldRenderer from './WidgetFieldRenderer.vue'

const props = defineProps<{
    field: WidgetFormFieldData
    modelValue: any
    disabled?: boolean
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void
}>()

/** Стандартные поля изображения — заполняются автоматически из ImagePicker */
const IMAGE_STANDARD_FIELDS = ['id', 'src', 'alt', 'title', 'description']

const items = computed<any[]>(() => (Array.isArray(props.modelValue) ? props.modelValue : []))

// Сворачивание элементов массива (только для обычных объектов)
const collapsedItems = reactive<Record<string, boolean>>({})

function collapseKey(itemIdx: number): string {
    return `${props.field.name}__item__${itemIdx}`
}

function isCollapsed(itemIdx: number): boolean {
    return collapsedItems[collapseKey(itemIdx)] ?? true
}

function toggleCollapse(itemIdx: number) {
    collapsedItems[collapseKey(itemIdx)] = !(collapsedItems[collapseKey(itemIdx)] ?? true)
}

function emitArray(newArr: any[]) {
    emit('update:modelValue', newArr)
}

function addItem() {
    const arr = [...items.value]
    const format = props.field.format

    if (format === 'image') {
        arr.push({ id: null, src: '', alt: '', title: '', description: '' })
    } else if (format === 'product') {
        arr.push({
            id: null,
            name: null,
            url: null,
            short: null,
            price: null,
            image_src: null,
            image_alt: null,
            image_next_src: null,
            image_next_alt: null,
        })
    } else {
        const newItem: Record<string, any> = {}
        for (const nf of props.field.nestedFields || []) {
            newItem[nf.name] = nf.default ?? null
        }
        arr.push(newItem)
        collapsedItems[collapseKey(arr.length - 1)] = true
    }

    emitArray(arr)
}

function removeItem(itemIdx: number) {
    const arr = [...items.value]
    arr.splice(itemIdx, 1)
    emitArray(arr)
}

/** Вложенные поля элемента массива (обычный объект) */
function nestedFieldInstances(itemIdx: number): WidgetFormFieldData[] {
    const arr = items.value
    const itemValue = (Array.isArray(arr) && arr[itemIdx]) ? arr[itemIdx] : {}
    const nested = props.field.nestedFields || []
    return nested.map(f => ({
        ...f,
        value: itemValue[f.name] !== undefined ? itemValue[f.name] : f.default ?? null,
    }))
}

/**
 * Дополнительные поля элемента массива изображений — все поля, кроме стандартных
 * (id, src, alt, title, description). Их пользователь заполняет вручную.
 */
function imageExtraFieldInstances(itemIdx: number): WidgetFormFieldData[] {
    if (!props.field.nestedFields) return []
    return nestedFieldInstances(itemIdx).filter(f => !IMAGE_STANDARD_FIELDS.includes(f.name))
}

/** Слияние значений вложенных полей элемента массива */
function onItemSave(itemIdx: number, vals: Record<string, any>) {
    const arr = [...items.value]
    if (!arr[itemIdx]) arr[itemIdx] = {}
    arr[itemIdx] = { ...arr[itemIdx], ...vals }
    emitArray(arr)
}

/** Выбор изображения элемента массива (мержим, чтобы сохранить доп. поля) */
function onImageChange(itemIdx: number, value: any) {
    const arr = [...items.value]
    if (value === null) {
        arr.splice(itemIdx, 1)
    } else {
        arr[itemIdx] = { ...(arr[itemIdx] || {}), ...value }
    }
    emitArray(arr)
}

/** Выбор товара элемента массива (полная замена значения) */
function onProductChange(itemIdx: number, value: any) {
    const arr = [...items.value]
    if (value === null) {
        arr.splice(itemIdx, 1)
    } else {
        arr[itemIdx] = { ...value }
    }
    emitArray(arr)
}
</script>

<style scoped>
.array-object-field {
    width: 100%;
}
.array-object-item {
    background: #f9fafb;
}

/* Раскладка элемента массива изображений: изображение слева, доп. поля справа */
.image-item-layout {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
}
.image-item-picker {
    flex: 0 0 auto;
    max-width: 260px;
}
.image-item-extra {
    flex: 1 1 auto;
    min-width: 220px;
}

/* Метки дополнительных полей изображения — слева от поля, поле на всю ширину */
.image-item-extra :deep(.el-form-item) {
    display: flex !important;
    flex-direction: row !important;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
}
.image-item-extra :deep(.el-form-item__label) {
    flex: 0 0 auto;
    white-space: nowrap;
    padding: 0;
    text-align: left;
    float: none;
    width: auto;
    line-height: 28px;
}
.image-item-extra :deep(.el-form-item__content) {
    flex: 1 1 auto;
    min-width: 0;
}
.image-item-extra :deep(.el-form-item__content .el-input),
.image-item-extra :deep(.el-form-item__content .el-textarea),
.image-item-extra :deep(.el-form-item__content .el-select),
.image-item-extra :deep(.el-form-item__content .el-input-number) {
    width: 100%;
}

.rotate-90 {
    transform: rotate(90deg);
}
</style>
