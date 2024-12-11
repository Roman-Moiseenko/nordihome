<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Создание нового товара</h1>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-row :gutter="10">
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="Название товара">
                            <el-input v-model="form.name" @change="onName"/>
                            <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                        </el-form-item>
                        <el-form-item label="Название для печати">
                            <el-input v-model="form.name_print" />
                            <div v-if="errors.name_print" class="text-red-700">{{ errors.name_print }}</div>
                        </el-form-item>
                        <el-form-item label="Ссылка">
                            <el-input v-model="form.slug" placeholder="Заполнится автоматически" clearable/>
                        </el-form-item>
                        <el-form-item label="Артикул">
                            <el-input v-model="form.code" />
                            <div v-if="errors.code" class="text-red-700">{{ errors.code }}</div>
                        </el-form-item>
                        <el-form-item label="Описание (комментарий)">
                            <el-input v-model="form.comment" type="textarea" rows="3" maxlength="255" show-word-limit/>
                        </el-form-item>
                    </el-form>
                </el-col>
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="Главная категория">
                            <el-select v-model="form.category_id" filterable>
                                <el-option v-for="item in categories" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.category_id" class="text-red-700">{{ errors.category_id }}</div>
                        </el-form-item>
                        <el-form-item label="Доп.категории">
                            <el-select v-model="form.categories" filterable multiple clearable>
                                <el-option v-for="item in categories" :value="item.id" :label="item.name"/>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Бренд">
                            <el-select v-model="form.brand_id" filterable>
                                <el-option v-for="item in brands" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.brand_id" class="text-red-700">{{ errors.brand_id }}</div>
                        </el-form-item>
                        <el-form-item label="Страна происхождения">
                            <el-select v-model="form.country_id" filterable clearable>
                                <el-option v-for="item in country" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.country_id" class="text-red-700">{{ errors.country_id }}</div>
                        </el-form-item>
                        <el-form-item label="Поставщик">
                            <el-select v-model="form.distributor_id" filterable clearable>
                                <el-option v-for="item in distributors" :value="item.id" :label="item.name"/>
                            </el-select>
                        </el-form-item>
                    </el-form>
                </el-col>
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="НДС">
                            <el-select v-model="form.vat_id" filterable>
                                <el-option v-for="item in vat" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.vat_id" class="text-red-700">{{ errors.vat_id }}</div>
                        </el-form-item>
                        <el-form-item label="Вид продукции ИС">
                            <el-select v-model="form.marking_type_id" filterable clearable>
                                <el-option v-for="item in markingType" :value="item.id" :label="item.name"/>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Ед.измерения">
                            <el-select v-model="form.measuring_id" @change="onMeasuring">
                                <el-option v-for="item in measuring" :value="item.id" :label="item.name"/>
                            </el-select>
                            <div v-if="errors.measuring_id" class="text-red-700">{{ errors.measuring_id }}</div>
                        </el-form-item>
                        <el-form-item label="Дробление количества">
                            <el-checkbox v-model="form.fractional" :checked="form.fractional" />
                        </el-form-item>
                    </el-form>
                </el-col>
            </el-row>
        </div>
        <el-button type="primary" @click="onCreate" class="mt-3">Создать товар</el-button>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive} from "vue";

const props = defineProps({
    errors: Object,
    categories: Array,
    brands: Array,
    country: Array,
    vat: Array,
    measuring: Array,
    markingType: Array,
    distributors: Array,
    title: {
        type: String,
        default: 'Создание нового товара',
    },
})
const form = reactive({
    name: null,
    name_print: null,
    slug: null,
    code: null,
    comment: null,
    category_id: null,
    categories: [],
    brand_id: null,
    country_id: null,
    distributor_id: null,
    vat_id: null,
    marking_type_id: null,
    measuring_id: null,
    fractional: false,
})

function onMeasuring(val) {
    props.measuring.forEach(function (item) {
        if (item.id === val && item.fractional === 1) form.fractional = true
        if (item.id === val && item.fractional === 0) form.fractional = false

    })
}
function onName() {
    if (form.name_print === null) form.name_print = form.name
}


function onCreate() {
    router.post(route('admin.product.store'), form)
}

</script>

<style scoped>

</style>
