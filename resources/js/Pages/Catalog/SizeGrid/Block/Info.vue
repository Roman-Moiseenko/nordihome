<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="12">
                <el-form-item label="Название сетки">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Измерения">
                    <el-select v-model="info.dimensions" multiple placeholder="Выберите измерения" style="width: 100%">
                        <el-option
                            v-for="dimension in catalogStore.sizeGridDimensions"
                            :key="dimension.code"
                            :label="dimension.name"
                            :value="dimension.code"
                        />
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>

        <el-divider content-position="left">Значения сетки</el-divider>

        <div v-for="code in info.dimensions" :key="code" class="mb-4">
            <div class="font-medium mb-2">{{ dimensionName(code) }}</div>

            <div class="mb-2">
                <el-row
                    v-for="(value, index) in info.values[code]"
                    :key="value._key"
                    :gutter="10"
                    align="middle"
                    class="mb-1 value-row"
                    draggable="true"
                    @dragstart="onDragStart(code, index)"
                    @dragover="onDragOver"
                    @drop="onDrop(code, index)"
                    @dragend="onDragEnd"
                >
                    <el-col :span="1">
                        <i class="fa-solid fa-grip-vertical text-gray-400" style="cursor: grab;"></i>
                    </el-col>
                    <el-col :span="6">
                        <el-input v-model="value.value" placeholder="Значение"/>
                    </el-col>
                    <el-col :span="6">
                        <el-input v-model="value.label" placeholder="Подпись (необязательно)"/>
                    </el-col>
                    <el-col :span="3">
                        <el-switch v-model="value.isActive"/>
                    </el-col>
                    <el-col :span="3">
                        <el-button size="small" type="danger" @click="removeValue(code, index)">
                            Удалить
                        </el-button>
                    </el-col>
                </el-row>
            </div>

            <el-button size="small" @click="addValue(code)">+ Добавить значение</el-button>
        </div>

        <div class="mt-4">
            <el-button v-if="hasChanges" type="info" @click="onCancel">Отмена</el-button>
            <el-button v-if="hasChanges" type="success" @click="onSetInfo">Сохранить</el-button>
        </div>
    </el-form>
</template>

<script setup>
import {reactive, computed, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {useCatalogStore} from "@Res/catalogStore.ts";

const props = defineProps({
    sizeGrid: Object,
})

const catalogStore = useCatalogStore();

// --- Исходные данные из пропсов (эталон для отмены и сравнения) ---
function buildValues() {
    const result = {};
    for (const dimension of (props.sizeGrid.dimensions || [])) {
        result[dimension.code] = (props.sizeGrid.values || [])
            .filter(value => value.dimensionCode === dimension.code)
            .map((value, index) => ({
                id: value.id,
                value: value.value,
                label: value.label,
                isActive: !!value.isActive,
                _key: value.id != null ? `v${value.id}` : `n${index}`,
            }));
    }
    return result;
}

const initialInfo = reactive({
    name: props.sizeGrid.name,
    dimensions: (props.sizeGrid.dimensions || []).map(dimension => dimension.code),
    values: buildValues(),
})

const info = reactive(JSON.parse(JSON.stringify(initialInfo)))

// Следим, чтобы для каждого выбранного измерения существовал массив значений
watch(
    () => info.dimensions,
    (codes) => {
        for (const code of codes) {
            ensureValues(code)
        }
    },
    {immediate: true},
)

// --- Перетаскивание значений ---
const drag = reactive({code: null, index: null})

function onDragStart(code, index) {
    drag.code = code
    drag.index = index
}

function onDragOver(event) {
    event.preventDefault()
}

function onDrop(code, targetIndex) {
    if (drag.code !== code || drag.index === null || drag.index === targetIndex) {
        return
    }
    const list = info.values[code];
    if (!Array.isArray(list)) return;

    const [moved] = list.splice(drag.index, 1)
    list.splice(targetIndex, 0, moved)
}

function onDragEnd() {
    drag.code = null
    drag.index = null
}

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    return JSON.stringify({
        name: info.name,
        dimensions: info.dimensions,
        values: info.values,
    }) !== JSON.stringify(initialInfo)
})

function dimensionName(code) {
    const fromProps = (props.sizeGrid.dimensions || []).find(dimension => dimension.code === code);
    if (fromProps) return fromProps.name;
    const fromStore = catalogStore.sizeGridDimensions.find(dimension => dimension.code === code);
    return fromStore ? fromStore.name : code;
}

function onCancel() {
    Object.assign(info, JSON.parse(JSON.stringify(initialInfo)))
}

function ensureValues(code) {
    if (!Array.isArray(info.values[code])) {
        info.values[code] = [];
    }
}

function addValue(code) {
    ensureValues(code)
    info.values[code].push({
        id: null,
        value: '',
        label: null,
        isActive: true,
        _key: `new-${Date.now()}-${Math.random()}`,
    })
}

function removeValue(code, index) {
    ensureValues(code)
    info.values[code].splice(index, 1)
}

function onSetInfo() {
    const values = [];

    for (const code of info.dimensions) {
        const list = info.values[code] || [];
        list.forEach((value, index) => {
            if (String(value.value).trim() === '') return;
            values.push({
                id: value.id,
                dimensionCode: code,
                value: String(value.value).trim(),
                label: value.label || null,
                sortOrder: index,
                isActive: !!value.isActive,
            });
        });
    }

    router.visit(
        route('admin.catalog.size-grid.update', {id: props.sizeGrid.id}), {
            method: "put",
            data: {
                name: info.name,
                dimensions: info.dimensions,
                values: values,
            },
            preserveState: false,
            preserveScroll: false,
        }
    );
}
</script>

<style scoped>
.value-row:hover {
    background-color: #f5f7fa;
}
</style>
