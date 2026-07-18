<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl mb-3">Карточка виджета</h1>
        <el-card>
            <el-form
                ref="formRef"
                :model="form"
                label-width="200px"
                label-position="left"
                :disabled="saving"
            >
                <el-row :gutter="10">
                    <el-col :span="8">
                        <el-divider content-position="center">Основная информация</el-divider>

                        <el-form-item label="Название" prop="name">
                            <el-input v-model="form.name" placeholder="Название виджета"/>
                        </el-form-item>

                        <el-form-item label="Slug" prop="slug">
                            <el-input v-model="form.slug" placeholder="url-slug"/>
                        </el-form-item>

                    </el-col>
                    <el-col :span="8">
                        <el-divider content-position="center">Продолжение</el-divider>

                        <el-form-item label="Категория" prop="category">
                            <el-select v-model="form.category" class="w-full">
                                <el-option
                                    v-for="(label, value) in contentStore.categories"
                                    :key="value"
                                    :value="value"
                                    :label="label"
                                />
                            </el-select>
                        </el-form-item>

                        <el-form-item label="Описание" prop="description">
                            <el-input
                                v-model="form.description"
                                type="textarea"
                                :rows="2"
                                placeholder="Описание виджета (необязательно)"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-divider content-position="center">Служебная информация</el-divider>

                        <el-descriptions :column="1" border>
                            <el-descriptions-item label="ID">
                                {{ widget.id }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Создан">
                                {{ widget.createdAt ? formatDate(widget.createdAt) : '—' }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Обновлён">
                                {{ widget.updatedAt ? formatDate(widget.updatedAt) : '—' }}
                            </el-descriptions-item>
                        </el-descriptions>

                    </el-col>
                </el-row>

                <el-divider content-position="left">Настройки схемы (Schema)</el-divider>
                <p class="text-gray-500 text-sm mb-3">
                    Определите поля виджета: их названия, типы и атрибуты.
                </p>

                <SchemaEditor
                    :schema="schemaConfig"
                    :key="schemaEditorKey"
                    @update:schema="onSchemaUpdate"
                >
                    <template #actions>
                        <el-button v-if="changeInfo" type="primary" :loading="saving" @click="onSaveInfo">
                            Сохранить
                        </el-button>
                        <el-button v-if="changeInfo" @click="onCancelInfo">Отмена</el-button>
                    </template>
                </SchemaEditor>

                <el-divider content-position="left">Шаблон отображения</el-divider>

                <p class="text-gray-500 text-sm mb-3">
                    Blade-шаблон для отображения виджета на сайте.
                    Файл: <code class="bg-gray-100 px-1 rounded">widgets/{{ form.category }}/{{ form.slug }}.blade.php</code>
                </p>
                <BladeEditor
                    v-model="templateContent"
                    :height="400"
                />
                <div class="flex justify-end mt-2">
                    <el-button type="primary" @click="saveTemplate" :loading="savingTemplate">
                        <el-icon><Check /></el-icon>
                        Сохранить шаблон
                    </el-button>
                </div>

            </el-form>
        </el-card>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {computed, ref, watch} from 'vue'
import {ElMessage} from 'element-plus'
import {Check} from '@element-plus/icons-vue'

import axios from 'axios'

import SchemaEditor from './Elements/SchemaEditor.vue'
import BladeEditor from './Elements/BladeEditor.vue'
import {useContentStore} from "@Res/contentStore";

const contentStore = useContentStore()

const props = defineProps({
    widget: Object,
    template: String,
    title: {
        type: String,
        default: 'Карточка виджета',
    },
})

// ============== Исходные данные из пропсов (эталон для отмены) ==============
const initialForm = {
    name: props.widget?.name ?? '',
    slug: props.widget?.slug ?? '',
    category: props.widget?.category ?? 'content',
    description: props.widget?.description ?? '',
}

// ============== Состояние ==============
const saving = ref(false)
const savingTemplate = ref(false)
const schemaEditorKey = ref(0)

// Форма для редактирования основных полей
const form = ref({...initialForm})

// Конфигурация схемы
const schemaConfig = ref<any>({type: 'object', properties: {}, required: []})

// Шаблон
const templateContent = ref(props.template ?? '')

// ============== Отслеживание изменений ==============
const changeInfo = ref(false)

watch(form, () => {
    checkChanges()
}, {deep: true})

function checkChanges() {
    for (const key of Object.keys(initialForm)) {
        const a = JSON.stringify((form.value as any)[key])
        const b = JSON.stringify((initialForm as any)[key])
        if (a !== b) {
            changeInfo.value = true
            return
        }
    }
    const origSchema = props.widget?.schema
    if (origSchema) {
        const a = JSON.stringify(schemaConfig.value)
        const b = JSON.stringify({
            type: origSchema.type || 'object',
            properties: origSchema.properties || {},
            required: origSchema.required || [],
        })
        if (a !== b) {
            changeInfo.value = true
            return
        }
    }
    changeInfo.value = false
}

// ============== Инициализация схемы из пропсов ==============
function initSchema() {
    if (props.widget?.schema) {
        schemaConfig.value = JSON.parse(JSON.stringify({
            type: props.widget.schema.type || 'object',
            properties: props.widget.schema.properties || {},
            required: props.widget.schema.required || [],
        }))
    }
    schemaEditorKey.value++
}

initSchema()

// ============== Форматирование даты ==============
function formatDate(dateStr: string): string {
    if (!dateStr) return '—'
    const d = new Date(dateStr)
    return d.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

// ============== Обновление схемы из SchemaEditor ==============
function onSchemaUpdate(newSchema: any) {
    schemaConfig.value = newSchema
    changeInfo.value = true
}

// ============== Сохранение / Отмена ==============
function onSaveInfo() {
    saving.value = true

    const schemaPayload = {
        type: 'object',
        properties: {...schemaConfig.value.properties},
        required: schemaConfig.value.required || [],
    }

    router.visit(route('admin.content.widget.update', {id: props.widget.id}), {
        method: 'put',
        data: {
            ...form.value,
            schema: schemaPayload,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            saving.value = false
            changeInfo.value = false
            contentStore.reload()
            ElMessage.success('Виджет сохранён')
        },
        onError: (errors) => {
            saving.value = false
            const firstError = Object.values(errors)[0]
            ElMessage.error(typeof firstError === 'string' ? firstError : 'Ошибка при сохранении')
        },
    })
}

function onCancelInfo() {
    form.value = {...initialForm}
    initSchema()
    changeInfo.value = false
}

// ============== Сохранение шаблона ==============
function saveTemplate() {
    savingTemplate.value = true

    axios.post(route('admin.content.widget.save-template', {id: props.widget.id}), {
        content: templateContent.value,
    })
        .then(() => {
            savingTemplate.value = false
            ElMessage.success('Шаблон сохранён')
        })
        .catch(() => {
            savingTemplate.value = false
            ElMessage.error('Ошибка при сохранении шаблона')
        })
}
</script>

<style scoped>
.font-mono {
    font-family: 'Courier New', Courier, monospace;
}
</style>
