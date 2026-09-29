<template>
    <!-- object с format=image -->
    <div v-if="field.format === 'image'" class="image-object-field w-full">
        <ImagePicker
            :model-value="modelValue || null"
            @update:model-value="onPick"
        />
    </div>

    <!-- object с format=product -->
    <div v-else-if="field.format === 'product'" class="product-object-field w-full">
        <ProductPicker
            :model-value="modelValue || null"
            @update:model-value="onPick"
        />
    </div>

    <!-- object с format=product_group -->
    <div v-else-if="field.format === 'product_group'" class="product-group-field w-full">
        <ProductGroupPicker
            :model-value="modelValue || null"
            @update:model-value="onPick"
        />
        <el-form-item label="Максимум товаров">
            <el-input-number
                :model-value="modelValue?.limit ?? 0"
                @update:model-value="onLimitChange"
                :min="0"
                :disabled="disabled"
                placeholder="Максимум товаров"
            />
        </el-form-item>
    </div>

    <!-- обычный объект — сворачиваемый -->
    <div v-else class="composite-field-wrapper border rounded-lg bg-white shadow-sm w-full">
        <!-- Шапка объекта -->
        <div
            class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
            @click="collapsed = !collapsed"
        >
            <el-icon class="text-gray-400" :class="{ 'rotate-90': collapsed }">
                <i :class="collapsed ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
            </el-icon>
            <span class="text-sm font-medium text-gray-600">{{ field.label || field.name }}</span>
        </div>

        <!-- Разворачиваемая часть — поля объекта -->
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <WidgetFieldRenderer
                :fields="nestedFieldInstances"
                :disabled="disabled"
                :showSaveButton="false"
                @save="onNestedSave"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import type { WidgetFormFieldData } from '@Res/composables/useContentBlock'
import ImagePicker from './ImagePicker.vue'
import ProductPicker from './ProductPicker.vue'
import ProductGroupPicker from './ProductGroupPicker.vue'
import WidgetFieldRenderer from './WidgetFieldRenderer.vue'

const props = defineProps<{
    field: WidgetFormFieldData
    modelValue: any
    disabled?: boolean
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void
}>()

const collapsed = ref(true)

/** Построить вложенные поля объекта из текущего значения */
const nestedFieldInstances = computed<WidgetFormFieldData[]>(() => {
    const val = (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue))
        ? props.modelValue
        : {}
    const nested = props.field.nestedFields || []
    return nested.map(f => ({
        ...f,
        value: val[f.name] !== undefined ? val[f.name] : f.default ?? null,
    }))
})

/** image / product / product_group — выбор значения */
function onPick(value: any) {
    if (value === null) {
        emit('update:modelValue', null)
    } else {
        emit('update:modelValue', { ...value })
    }
}

/** product_group — ограничение «Максимум товаров» */
function onLimitChange(value: number | null) {
    const current = (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue))
        ? { ...props.modelValue }
        : { entity_type: null, entity_id: null, title: null }
    current.limit = value ?? 0
    emit('update:modelValue', current)
}

/** Слияние значений вложенных полей объекта */
function onNestedSave(vals: Record<string, any>) {
    emit('update:modelValue', {
        ...(props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue) ? props.modelValue : {}),
        ...vals,
    })
}
</script>

<style scoped>
.rotate-90 {
    transform: rotate(90deg);
}
</style>
