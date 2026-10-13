<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-ruler-combined"></i>
                <span> Размерный ряд</span>
            </span>
        </template>

        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <!-- Сетка не выбрана -->
        <div v-else-if="form.sizeGridId == null" class="py-4">
            <el-form label-width="auto">
                <el-form-item label="Размерная сетка">
                    <el-select v-model="selectedGridId" placeholder="Выберите сетку" style="width: 320px">
                        <el-option
                            v-for="grid in form.sizeGrids"
                            :key="grid.id"
                            :label="grid.name"
                            :value="grid.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" :disabled="selectedGridId == null" @click="applyGrid">
                        Применить
                    </el-button>
                </el-form-item>
            </el-form>
            <p class="text-gray-400 text-sm">
                При создании размерного ряда автоматически заполняются все возможные
                комбинации осей выбранной сетки и становятся доступными.
            </p>
        </div>

        <!-- Сетка выбрана -->
        <div v-else>
            <div class="flex items-center gap-2 mb-3">
                <span class="text-gray-600">Сетка:</span>
                <el-select v-model="selectedGridId" style="width: 280px">
                    <el-option
                        v-for="grid in form.sizeGrids"
                        :key="grid.id"
                        :label="grid.name"
                        :value="grid.id"
                    />
                </el-select>
                <el-button type="primary" plain :disabled="selectedGridId == null" @click="applyGrid">
                    Применить
                </el-button>
                <el-button type="danger" plain @click="removeGrid">
                    Отвязать сетку
                </el-button>
            </div>

            <!-- Одна ось — размеры друг за другом, не более 6 в строке -->
            <div v-if="isSingleAxis" class="sizes-grid">
                <button
                    v-for="row in rows"
                    :key="row.id"
                    type="button"
                    class="size-box"
                    :class="{ inactive: !row.isActive }"
                    @click="toggleSize(row)"
                >
                    {{ labelOf(axisSize, row.values[axisSize]) }}
                </button>
            </div>

            <!-- Две оси — колонки (вторая ось) и размеры внутри -->
            <div v-else class="size-columns">
                <div v-for="column in columns" :key="column.value" class="size-column">
                    <div class="column-header" @click="toggleColumn(column)">
                        {{ column.label }}
                    </div>
                    <div class="column-sizes">
                        <button
                            v-for="row in column.sizes"
                            :key="row.id"
                            type="button"
                            class="size-box"
                            :class="{ inactive: !row.isActive }"
                            @click="toggleSize(row)"
                        >
                            {{ labelOf(axisSize, row.values[axisSize]) }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <el-button type="primary" @click="onSave">Сохранить размеры</el-button>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch} from "vue";
import api from "@Res/api";
import {route} from "ziggy-js";
import {useProductPanel} from "./useProductPanel";

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: 'size-grid',
    },
})

const isSaving = ref(false)

const form = reactive({
    id: props.productId,
    sizeGridId: null,
    sizeGrids: [],
    sizeGrid: null,
    productSizes: [],
})

const selectedGridId = ref(null)

const {load, loading, loaded, setCache} = useProductPanel('size-grid', async () => {
    return api.get(
        route('admin.catalog.product.edit.size-grid', {id: props.productId}),
        null,
        {showSuccess: false},
    )
})

function applyData(data) {
    form.id = data.id
    form.sizeGridId = data.sizeGridId
    form.sizeGrids = Array.isArray(data.sizeGrids) ? data.sizeGrids.map(g => ({...g})) : []
    form.sizeGrid = data.sizeGrid ? JSON.parse(JSON.stringify(data.sizeGrid)) : null
    form.productSizes = Array.isArray(data.productSizes) ? data.productSizes.map(s => ({...s})) : []
    selectedGridId.value = data.sizeGridId
}

const axisSize = computed(() => form.sizeGrid?.dimensions?.[0]?.code ?? 'size')
const axisColumn = computed(() => form.sizeGrid?.dimensions?.[1]?.code ?? null)

const sizeValues = computed(() => {
    const list = (form.sizeGrid?.values || []).filter(v => v.dimensionCode === axisSize.value)
    return [...list].sort((a, b) => (a.sortOrder ?? 0) - (b.sortOrder ?? 0))
})

const columnValues = computed(() => {
    if (!axisColumn.value) return []
    const list = (form.sizeGrid?.values || []).filter(v => v.dimensionCode === axisColumn.value)
    return [...list].sort((a, b) => (a.sortOrder ?? 0) - (b.sortOrder ?? 0))
})

const isSingleAxis = computed(() => axisColumn.value == null)

const sizeOrder = computed(() => {
    const map = new Map()
    sizeValues.value.forEach((v, index) => map.set(v.value, index))
    return map
})

// Одна ось: все размеры в порядке сетки.
const rows = computed(() => {
    const list = form.productSizes || []
    return [...list].sort((a, b) => {
        const order = sizeOrder.value
        return (order.get(a.values?.[axisSize.value]) ?? 9999) - (order.get(b.values?.[axisSize.value]) ?? 9999)
    })
})

// Две оси: вторая ось — колонки, внутри колонки — размеры первой оси.
const columns = computed(() => {
    if (!axisColumn.value) return []

    const order = sizeOrder.value
    const map = new Map()

    for (const cv of columnValues.value) {
        map.set(cv.value, {
            value: cv.value,
            label: cv.label || cv.value,
            sizes: [],
        })
    }

    for (const row of (form.productSizes || [])) {
        const colVal = row.values?.[axisColumn.value]
        if (map.has(colVal)) {
            map.get(colVal).sizes.push(row)
        }
    }

    for (const column of map.values()) {
        column.sizes.sort((a, b) => {
            return (order.get(a.values?.[axisSize.value]) ?? 9999) - (order.get(b.values?.[axisSize.value]) ?? 9999)
        })
    }

    return [...map.values()].filter(column => column.sizes.length > 0)
})

function labelOf(code, value) {
    const item = (form.sizeGrid?.values || []).find(v => v.dimensionCode === code && v.value === value)
    if (item && item.label) return item.label
    return value ?? ''
}

function toggleSize(row) {
    row.isActive = !row.isActive
}

function toggleColumn(column) {
    const allActive = column.sizes.every(s => s.isActive)
    column.sizes.forEach(s => { s.isActive = !allActive })
}

function save(payload) {
    isSaving.value = true

    api.post(
        route('admin.catalog.product.edit.size-grid', {id: props.productId}),
        payload,
    ).then(data => {
        applyData(data)
        setCache(data)
    }).finally(() => {
        isSaving.value = false
    })
}

function applyGrid() {
    if (selectedGridId.value == null) return
    save({
        id: form.id,
        sizeGridId: selectedGridId.value,
        sizes: [],
    })
}

function removeGrid() {
    save({
        id: form.id,
        sizeGridId: null,
        sizes: [],
    })
}

function onSave() {
    save({
        id: form.id,
        sizeGridId: form.sizeGridId,
        sizes: (form.productSizes || []).map(s => ({
            id: s.id,
            values: s.values,
            isActive: !!s.isActive,
        })),
    })
}

watch(() => props.active, async (active) => {
    if (!active) return
    const {data, fromCache} = await load()
    if (!fromCache && data) {
        applyData(data)
    }
}, {immediate: true})
</script>

<style scoped>
.size-box {
    width: 44px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 4px;
    padding: 6px;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    background: #fff;
    color: #333;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.size-box:hover {
    border-color: #409eff;
    color: #409eff;
}

.size-box.inactive {
    background: #f0f0f0;
    border-color: #e0e0e0;
    color: #b0b0b0;
}

.sizes-grid {
    display: flex;
    flex-wrap: wrap;
    max-width: calc(6 * 44px + 6 * 8px);
}

.size-column {
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    padding: 8px;
}

.column-header {
    cursor: pointer;
    padding: 6px 8px;
    margin-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
    background: #fafafa;
    text-align: center;
    font-weight: 500;
    user-select: none;
}

.column-header:hover {
    background: #e6f1fc;
}

.column-sizes {
    display: flex;
    flex-wrap: wrap;
    max-width: calc(6 * 44px + 6 * 8px);
}
</style>
