<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-file-invoice"></i>
                <span> Общие параметры</span>
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

        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Название товара">
                        <el-input v-model="form.name" @change="onAutoSave" :disabled="isSaving"/>
                        <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                    </el-form-item>
                    <el-form-item label="Название для печати">
                        <el-input v-model="form.namePrint" @change="onAutoSave" :disabled="isSaving" />
                        <div v-if="errors.namePrint" class="text-red-700">{{ errors.namePrint }}</div>
                    </el-form-item>
                    <el-form-item label="Ссылка">
                        <el-input v-model="form.slug" @change="onAutoSave" :disabled="isSaving" placeholder="Заполнится автоматически" clearable/>
                    </el-form-item>
                    <el-form-item label="Артикул">
                        <el-input v-model="form.code" @change="onAutoSave" :disabled="isSaving" />
                        <div v-if="errors.code" class="text-red-700">{{ errors.code }}</div>
                    </el-form-item>
                    <el-form-item label="Описание (комментарий)">
                        <el-input v-model="form.comment" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="3" maxlength="255" show-word-limit/>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Главная категория">
                        <el-select v-model="form.categoryId" @change="onAutoSave" :disabled="isSaving" filterable>
                            <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.categoryId" class="text-red-700">{{ errors.categoryId }}</div>
                    </el-form-item>
                    <el-form-item label="Доп.категории">
                        <el-select v-model="form.categories" @change="onAutoSave" :disabled="isSaving" filterable multiple clearable>
                            <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Комнаты">
                        <el-select v-model="form.rooms" @change="onAutoSave" :disabled="isSaving" filterable multiple clearable>
                            <el-option v-for="item in useCatalog.rooms" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Бренд">
                        <el-select v-model="form.brandId" @change="onAutoSave" :disabled="isSaving" filterable>
                            <el-option v-for="item in useCatalog.brands" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.brandId" class="text-red-700">{{ errors.brandId }}</div>
                    </el-form-item>
                    <el-form-item label="Страна происхождения">
                        <el-select v-model="form.countryId" @change="onAutoSave" :disabled="isSaving" filterable clearable>
                            <el-option v-for="item in useGuide.countries" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.countryId" class="text-red-700">{{ errors.countryId }}</div>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 3 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Вид продукции ИС">
                        <el-select v-model="form.markingTypeId" @change="onAutoSave" :disabled="isSaving" filterable clearable>
                            <el-option v-for="item in useGuide.markingType" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Ед.измерения">
                        <el-select v-model="form.measuringId" @change="onAutoSave" :disabled="isSaving" >
                            <el-option v-for="item in useGuide.measuring" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.measuringId" class="text-red-700">{{ errors.measuringId }}</div>
                    </el-form-item>
                    <el-form-item label="Дробление количества">
                        <el-checkbox v-model="form.fractional" @change="onAutoSave" :disabled="isSaving" :checked="form.fractional" />
                    </el-form-item>
                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, computed, watch, defineProps } from "vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import api from "@Res/api";
import {route} from "ziggy-js";
import {useGuideStore} from "@Res/guideStore";
import {useProductPanel} from "./useProductPanel";

const useCatalog = useCatalogStore()
const useGuide = useGuideStore()

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
        default: 'common',
    },
})

const autoSave = ref(true)
const isSaving = ref(false)
const hasModification = ref(false)

const form = reactive({
    id: props.productId,
    name: '',
    namePrint: '',
    slug: '',
    code: '',
    comment: '',
    categoryId: null,
    categories: [],
    rooms: [],
    brandId: null,
    countryId: null,
    markingTypeId: null,
    measuringId: null,
    fractional: false,
    modification: false,
})

// Ошибки валидации (приходят из ответа на сохранение).
const saveErrors = reactive({})
const errors = computed(() => ({ ...saveErrors }))

// Загрузка данных панели с кэшем на 5 минут.
const { load, loading, loaded, setCache } = useProductPanel('common', async () => {
    return api.get(
        route('admin.catalog.product.edit.common', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

function applyData(data) {
    form.id = data.id
    form.name = data.name
    form.namePrint = data.namePrint
    form.slug = data.slug
    form.code = data.code
    form.comment = data.comment
    form.categoryId = data.categoryId
    form.categories = Array.isArray(data.categories) ? data.categories : []
    form.rooms = Array.isArray(data.rooms) ? data.rooms : []
    form.brandId = data.brandId
    form.countryId = data.countryId
    form.markingTypeId = data.markingTypeId
    form.measuringId = data.measuringId
    form.fractional = data.fractional
    hasModification.value = data.hasModification
}

// Автозагрузка при активации панели.
// Если данные уже загружались — повторно не запрашиваем (кэш 5 минут),
// а форму не перезатираем, чтобы не потерять несохранённые изменения.
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
        route('admin.catalog.product.edit.common', { id: props.productId }),
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
</script>

<style scoped>

</style>
