<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-file-invoice"></i>
                <span> Общие параметры</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Название товара">
                        <el-input v-model="form.name" @change="onAutoSave" :disabled="isSaving"/>
                        <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                    </el-form-item>
                    <el-form-item label="Название для печати">
                        <el-input v-model="form.name_print" @change="onAutoSave" :disabled="isSaving" />
                        <div v-if="errors.name_print" class="text-red-700">{{ errors.name_print }}</div>
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
                        <el-select v-model="form.category_id" @change="onAutoSave" :disabled="isSaving" filterable>
                            <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.category_id" class="text-red-700">{{ errors.category_id }}</div>
                    </el-form-item>
                    <el-form-item label="Доп.категории">
                        <el-select v-model="form.categories" @change="onAutoSave" :disabled="isSaving" filterable multiple clearable>
                            <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Бренд">
                        <el-select v-model="form.brand_id" @change="onAutoSave" :disabled="isSaving" filterable>
                            <el-option v-for="item in brands" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.brand_id" class="text-red-700">{{ errors.brand_id }}</div>
                    </el-form-item>
                    <el-form-item label="Страна происхождения">
                        <el-select v-model="form.country_id" @change="onAutoSave" :disabled="isSaving" filterable clearable>
                            <el-option v-for="item in country" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.country_id" class="text-red-700">{{ errors.country_id }}</div>
                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 3 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="НДС">
                        <el-select v-model="form.vat_id" @change="onAutoSave" :disabled="isSaving" filterable>
                            <el-option v-for="item in vat" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.vat_id" class="text-red-700">{{ errors.vat_id }}</div>
                    </el-form-item>
                    <el-form-item label="Вид продукции ИС">
                        <el-select v-model="form.marking_type_id" @change="onAutoSave" :disabled="isSaving" filterable clearable>
                            <el-option v-for="item in markingType" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Ед.измерения">
                        <el-select v-model="form.measuring_id" @change="onAutoSave" :disabled="isSaving" >
                            <el-option v-for="item in measuring" :key="item.id" :value="item.id" :label="item.name"/>
                        </el-select>
                        <div v-if="errors.measuring_id" class="text-red-700">{{ errors.measuring_id }}</div>
                    </el-form-item>
                    <el-form-item label="Дробление количества">
                        <el-checkbox v-model="form.fractional" @change="onAutoSave" :disabled="isSaving" :checked="form.fractional" />
                    </el-form-item>
                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>
    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps } from "vue";
import {router} from "@inertiajs/vue3";


const props = defineProps({
    product: Object,
    errors: Object,
    categories: Array,
    brands: Array,
    country: Array,
    vat: Array,
    measuring: Array,
    markingType: Array,
})
console.log('Common', props.product.slug)
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    id: props.product.id,
    name: props.product.name,
    name_print: props.product.name_print,
    slug: props.product.slug,
    code: props.product.code,
    comment: props.product.comment,
    category_id: props.product.main_category_id,
    categories: [...props.product.categories.map(item => item.id)],
    brand_id: props.product.brand_id,
    country_id: props.product.country_id,
    //distributor_id: null,
    vat_id: props.product.vat_id,
    marking_type_id: props.product.marking_type_id,
    measuring_id: props.product.measuring_id,
    fractional: props.product.fractional,
})
function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.common', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            form.slug = page.props.product.slug
        },
        onError: page => {
            isSaving.value = false

        },
    })
}
</script>

<style scoped>

</style>
