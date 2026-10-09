<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-pallet-boxes"></i>
                <span> Атрибуты</span>
            </span>
        </template>
        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <div v-else>
            <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
            <el-checkbox v-if="hasModification"
                v-model="form.modification"
                :checked="form.modification" class="checkbox-warning">
                Сохранять для всех товаров из Модификации
            </el-checkbox>
            <el-row :gutter="10" class="mt-2 attribute-block">
                <el-col :span="8">
                    <el-form label-width="auto">
                        <div v-for="(attribute, index) in form.attributes" class="flex mt-2">
                            <el-form-item :label="attribute.group + '\\' + attribute.name" label-position="top"
                                          class="ml-2">
                                <!-- Вариант -->
                                <el-select v-if="attribute.is_variant" v-model="attribute.value"
                                           :multiple="attribute.multiple" @change="onAutoSave" :disabled="isSaving || attribute.is_modification">
                                    <el-option v-for="item in attribute.variants" :key="item.id" :value="item.id"
                                               :label="item.name"/>
                                </el-select>
                                <!-- Флажок -->
                                <el-checkbox v-if="attribute.is_bool" v-model="attribute.value" :label="attribute.name"
                                             :checked="attribute.value" @change="onAutoSave" :disabled="isSaving"/>

                                <!-- Число -->
                                <el-input v-if="attribute.is_numeric" v-model="attribute.value" :label="attribute.name"
                                          :formatter="val => func.MaskFloat(val)" @change="onAutoSave" :disabled="isSaving"/>
                                <!-- Строка -->
                                <el-input v-if="attribute.is_string" v-model="attribute.value" :label="attribute.name"
                                          @change="onAutoSave" :disabled="isSaving"/>
                            </el-form-item>
                            <el-form-item label="Удалить" label-position="top" class="ml-2">
                                <el-button type="danger" @click="onRemoveAttribute(index)" :disabled="attribute.is_modification"><i
                                    class="fa-light fa-trash"></i></el-button>
                            </el-form-item>
                        </div>
                    </el-form>
                    <div class="flex items-center mt-5">
                        <el-select v-model="new_attribute" placeholder="Выбрать атрибут">
                            <el-option v-for="item in form.possibleAttributes" :value="item.id" :label="item.name"/>
                        </el-select>
                        <el-button type="success" @click="onAddAttribute" class="ml-2">Добавить</el-button>
                    </div>
                </el-col>
            </el-row>
            <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch, defineProps} from "vue"
import {func} from "@Res/func"
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
        default: 'attribute',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)

const form = reactive({
    id: props.productId,
    attributes: [],
    possibleAttributes: [],
    modification: false,
})

// Ошибки валидации (приходят из ответа на сохранение).
const saveErrors = reactive({})
const errors = computed(() => ({ ...saveErrors }))

// Загрузка данных панели с кэшем на 5 минут.
const { load, loading, loaded, setCache } = useProductPanel('attribute', async () => {
    return api.get(
        route('admin.catalog.product.edit.attribute', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

function applyData(data) {
    form.id = data.id
    form.attributes = Array.isArray(data.attributes) ? data.attributes.map(a => ({ ...a })) : []
    form.possibleAttributes = Array.isArray(data.possibleAttributes) ? data.possibleAttributes.map(a => ({ ...a })) : []
    hasModification.value = data.hasModification
}

// Автозагрузка при активации панели.
watch(() => props.active, async (active) => {
    if (!active) return
    const { data, fromCache } = await load()
    if (!fromCache && data) {
        applyData(data)
    }
}, { immediate: true })


function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}

function onSave() {
    isSaving.value = true;
    Object.keys(saveErrors).forEach(key => delete saveErrors[key])

    api.post(
        route('admin.catalog.product.edit.attribute', { id: props.productId }),
        { ...form },
    ).then(data => {
        applyData(data)
        setCache(data)
    }).catch(error => {
        const errs = error?.response?.data?.errors
        if (errs && typeof errs === 'object') {
            for (const [key, value] of Object.entries(errs)) {
                saveErrors[key] = Array.isArray(value) ? value[0] : value
            }
        }
    }).finally(() => {
        isSaving.value = false
    })
}

const new_attribute = ref(null)

function onRemoveAttribute(index) {
    form.attributes.splice(index, 1)
    onAutoSave()
}

function onAddAttribute() {
    form.attributes.push({
        id: new_attribute.value,
        group: 'Идет сохранение',
        name: '...',
    })
    onSave()
    new_attribute.value = null;
}
</script>

<style lang="scss">
.attribute-block {
    .el-input {
        --el-input-width: 300px;
    }

    .el-select {
        --el-select-width: 300px;
    }
}
</style>
