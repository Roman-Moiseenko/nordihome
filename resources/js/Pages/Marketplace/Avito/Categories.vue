<template>
    <Head><title>Категории Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категории Авито</h1>

        <div class="flex my-3">
            <el-upload
                :auto-upload="false"
                :limit="1"
                accept=".xlsx"
                :on-change="onFileChange"
                :on-remove="() => (file = null)"
                :show-file-list="false"
            >
                <el-button>Выбрать английский шаблон</el-button>
            </el-upload>
            <el-button
                type="primary"
                class="ml-3"
                :disabled="!file"
                :loading="uploading"
                @click="upload"
            >
                Загрузить структуру из шаблона
            </el-button>
            <div class="text-gray-500 text-sm my-auto">
                Шаблон с английскими названиями, сверяется с реестром параметров.
            </div>
        </div>

        <div class="flex gap-4">
            <div class="p-5 bg-white rounded-md" style="flex: 1; max-width: 460px;">
                <el-tree
                    :data="categories"
                    node-key="id"
                    :props="{ label: 'name', children: 'children' }"
                    :expand-on-click-node="true"
                    default-expand-all
                    @node-click="onNodeClick"
                />
            </div>

            <div class="p-5 bg-white rounded-md" style="flex: 1;" v-loading="loading">
                <template v-if="!categoryId">
                    <div class="text-gray-400">Выберите конечную категорию из списка</div>
                </template>
                <template v-else>
                    <div class="font-medium mb-3">Параметры категории</div>
                    <el-table :data="categoryFields" header-cell-class-name="nordihome-header">
                        <el-table-column label="Название" min-width="200">
                            <template #default="scope">
                                {{ scope.row.name }}
                                <el-tag v-if="scope.row.is_required" type="danger" size="small" class="ml-1">
                                    Обязательный
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Тип" width="160" align="center">
                            <template #default="scope">
                                <el-select
                                    v-if="scope.row.type === null"
                                    size="small"
                                    placeholder="Указать тип"
                                    @change="(type) => onSaveType(scope.row, type)"
                                >
                                    <el-option label="Строка" value="string"/>
                                    <el-option label="Массив строк" value="array"/>
                                </el-select>
                                <el-tag v-else size="small">{{ typeLabel(scope.row.type) }}</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="Варианты" min-width="260">
                            <template #default="scope">
                                <el-select
                                    v-if="canEditVariants(scope.row)"
                                    v-model="scope.row.allowed_values"
                                    multiple
                                    filterable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    size="small"
                                    placeholder="Добавить вариант"
                                    style="width: 100%;"
                                    @change="(values) => onSaveVariants(scope.row, values)"
                                />
                                <template v-else-if="scope.row.allowed_values && scope.row.allowed_values.length">
                                    <el-tag
                                        v-for="value in scope.row.allowed_values"
                                        :key="value"
                                        size="small"
                                        class="mr-1"
                                    >
                                        {{ value }}
                                    </el-tag>
                                </template>
                                <span v-else class="text-gray-400">—</span>
                            </template>
                        </el-table-column>
                    </el-table>
                </template>
            </div>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { route } from "ziggy-js";
import { useAvitoCategoryFields } from "@Res/composables/useAvitoCategoryFields";

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const {
    fields: categoryFields,
    loading,
    categoryId,
    load,
    saveType,
    saveVariants,
} = useAvitoCategoryFields()

const file = ref<File | null>(null)
const uploading = ref(false)

function onFileChange(uploadFile: any) {
    file.value = uploadFile.raw ?? null
}

function upload() {
    if (!file.value) return

    uploading.value = true
    const form = new FormData()
    form.append('file', file.value)

    router.post(route('admin.marketplace.avito.import'), form, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            uploading.value = false
        },
    })
}

function onNodeClick(data: any) {
    if (data.children && data.children.length) {
        return
    }
    load(data.id)
}

function onSaveType(row: any, type: string) {
    saveType(row.id, type)
}

function canEditVariants(row: any) {
    return (row.type === 'string' || row.type === 'array') && row.is_html !== true
}

function onSaveVariants(row: any, values: string[]) {
    saveVariants(row.id, values)
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
