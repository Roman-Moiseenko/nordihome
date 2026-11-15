<template>
    <Head><title>Реестр параметров Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Реестр параметров Авито</h1>

        <div class="flex my-3 items-start gap-3">
            <el-upload
                :auto-upload="false"
                :limit="1"
                accept=".xlsx"
                :on-change="(f) => onFileChange('ru', f)"
                :on-remove="() => (files.ru = null)"
                :show-file-list="false"
            >
                <el-button>Русский файл</el-button>
            </el-upload>
            <el-upload
                :auto-upload="false"
                :limit="1"
                accept=".xlsx"
                :on-change="(f) => onFileChange('en', f)"
                :on-remove="() => (files.en = null)"
                :show-file-list="false"
            >
                <el-button>Английский файл</el-button>
            </el-upload>
            <el-button
                type="primary"
                :disabled="!files.ru || !files.en"
                :loading="uploading"
                @click="upload"
            >
                Загрузить в реестр
            </el-button>
            <div class="text-gray-500 text-sm my-auto">
                Выбранные файлы: <b>{{ fileNames }}</b>
            </div>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table :data="registry" header-cell-class-name="nordihome-header" row-key="id">
                <el-table-column prop="nameEn" label="Английское название" min-width="220"/>
                <el-table-column prop="nameRu" label="Русское название" min-width="220"/>
                <el-table-column label="Вид" width="260">
                    <template #default="scope">
                        <el-select
                            v-model="scope.row.kind"
                            size="small"
                            clearable
                            placeholder="Не определён"
                            style="width: 100%;"
                            @change="(kind) => onSaveKind(scope.row, kind)"
                        >
                            <el-option
                                v-for="kind in kinds"
                                :key="kind.value"
                                :value="kind.value"
                                :label="kind.label"
                            />
                        </el-select>
                    </template>
                </el-table-column>
                <el-table-column label="Поле товара" width="280">
                    <template #default="scope">
                        <el-select
                            v-if="scope.row.kind === 'product'"
                            v-model="scope.row.field"
                            size="small"
                            filterable
                            clearable
                            placeholder="Выберите поле"
                            style="width: 100%;"
                            @change="(field) => onSaveField(scope.row, field)"
                        >
                            <el-option
                                v-for="item in productFieldOptions"
                                :key="item.value"
                                :value="item.value"
                                :label="item.label"
                            />
                        </el-select>
                        <span v-else class="text-gray-400">—</span>
                    </template>
                </el-table-column>
                <el-table-column label="Тип" width="130" align="center">
                    <template #default="scope">
                        <el-tag v-if="scope.row.type" size="small">{{ typeLabel(scope.row.type) }}</el-tag>
                        <span v-else class="text-gray-400">—</span>
                    </template>
                </el-table-column>
                <el-table-column label="Обязательный" width="120" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.isRequired"/>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    registry: {
        type: Array,
        default: () => [],
    },
    productFields: {
        type: Object,
        default: () => ({}),
    },
    kinds: {
        type: Array,
        default: () => [],
    },
})

const registry = ref<any[]>([...props.registry])
const files = ref<{ ru: File | null, en: File | null }>({ ru: null, en: null })
const uploading = ref(false)

const productFieldOptions = computed(() =>
    Object.entries(props.productFields).map(([value, label]) => ({ value, label: label as string }))
)

const fileNames = computed(() => {
    const parts: string[] = []
    if (files.value.ru) parts.push(files.value.ru.name)
    if (files.value.en) parts.push(files.value.en.name)
    return parts.length ? parts.join(', ') : 'файлы не выбраны'
})

function onFileChange(key: 'ru' | 'en', uploadFile: any) {
    files.value[key] = uploadFile.raw ?? null
}

function upload() {
    if (!files.value.ru || !files.value.en) return

    uploading.value = true
    const form = new FormData()
    form.append('file_ru', files.value.ru)
    form.append('file_en', files.value.en)

    router.post(route('admin.marketplace.avito.registry.import'), form, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            uploading.value = false
        },
    })
}

async function onSaveKind(row: any, kind: string | null) {
    row.field = kind === 'product' ? row.field : null
    await save(row, kind, row.field)
}

async function onSaveField(row: any, field: string | null) {
    await save(row, row.kind, field)
}

async function save(row: any, kind: string | null, field: string | null) {
    const { data } = await axios.post(
        route('admin.marketplace.avito.registry.update', { id: row.id }),
        { kind, field }
    )

    row.kind = data.kind
    row.field = data.field
}

function typeLabel(type: string) {
    const labels: Record<string, string> = {
        string: 'Строка',
        integer: 'Число',
        float: 'Дробное число',
        array: 'Массив строк',
    }
    return labels[type] ?? type
}
</script>
