<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="6">
            <el-image
                style="width: 200px; height: 200px"
                :src="attribute.image"
                :zoom-rate="1.2"
                :max-scale="7"
                :min-scale="0.2"
                :initial-index="4"
                :preview-src-list="[attribute.image]"
                fit="cover"
            />
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Группа">
                    {{ attribute.group }}
                </el-descriptions-item>
                <el-descriptions-item label="Категории">
                    <el-tag v-for="item in attribute.categories" class="ml-1">{{ item.name }}</el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Тип">
                    {{ attribute.type_text }}
                    <div v-if="attribute.is_variant" v-for="item in attribute.variants" class="flex mb-1">

                        <el-tag type="info" class="my-auto ml-1">{{ item.name }}</el-tag>
                        <el-image v-if="item.image"
                            style="width: 40px; height: 40px"
                            :src="item.image"
                            :zoom-rate="1.2"
                            :max-scale="7"
                            :min-scale="0.2"
                            :initial-index="4"
                            :preview-src-list="[item.image]"
                            fit="cover"
                            class="ml-2"
                        />
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="Множественный выбор">
                    <Active :active="attribute.multiple"/>
                </el-descriptions-item>
                <el-descriptions-item label="Фильтр">
                    <Active :active="attribute.filter"/>
                </el-descriptions-item>
                <el-descriptions-item label="Показывать в поиске">
                    <Active :active="attribute.show_in"/>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>

    </el-row>
    <el-button v-if="!showEdit" class="ml-2" type="warning" @click="showEdit = true">
        <i class="fa-light fa-pen-to-square"></i>&nbsp;Редактировать
    </el-button>
    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Название атрибута">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Категория">
                    <el-select v-model="info.categories" filterable multiple>
                        <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Группа">
                    <el-select v-model="info.group_id" filterable>
                        <el-option v-for="item in groups" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Ссылка на википедию">
                    <el-input v-model="info.sameAs"/>
                </el-form-item>
                <el-form-item label="Множественный выбор">
                    <el-checkbox v-model="info.multiple" :checked="info.multiple"/>
                </el-form-item>
                <el-form-item label="Используется для фильтрации">
                    <el-checkbox v-model="info.filter" :checked="info.filter"/>
                </el-form-item>
                <el-form-item label="Показывать в поиске и описании">
                    <el-checkbox v-model="info.show_in" :checked="info.show_in"/>
                </el-form-item>
                <el-form-item label="Тип значения атрибута ">
                    <el-select v-model="info.type">
                        <el-option v-for="item in types" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>

                <div v-if="info.type === variant" class="mb-5">
                    <h2>Варианты</h2>
                    <VarianField
                        v-for="item in Variants" :key="item"
                        :name="item.name"
                        :image="item.image"
                        @update:fields="val => onUpdateVariant(val, item.identity)"
                        @remove:fields="onRemoveVariant(item.identity)"
                    />
                    <el-button @click="addVariant">Добавить вариант</el-button>
                </div>
                <el-button type="info" @click="showEdit = false" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="4">
            <UploadImageFile
                label="Изображение для сайта"
                v-model:image="attribute.image"
                @selectImageFile="onSelectImage"
            />
        </el-col>
        <el-col :span="8">
            <div class="bg-warning/20 border border-warning rounded-md relative p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     icon-name="lightbulb" data-lucide="lightbulb"
                     class="lucide lucide-lightbulb w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3">
                    <line x1="9" y1="18" x2="15" y2="18"></line>
                    <line x1="10" y1="22" x2="14" y2="22"></line>
                    <path
                        d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 018.91 14"></path>
                </svg>
                <h2 class="text-lg font-medium">
                    Информация
                </h2>
                <div class="mt-5 font-medium"></div>
                <div class="leading-relaxed mt-2 text-slate-600">
                    <div><b>Название атрибута</b> не является уникальным полем, для несмежных категорий оно может
                        совпадать.
                    </div>
                    <div>Поле <b>категория</b> привязывает атрибут к категории и его дочерним категориям.</div>
                    <div class="mt-2">Поле <b>группа</b> позволяет сгруппировать характеристики на странице товара.
                    </div>
                    <div class="mt-2">Для <b>картинок</b> используйте форматы с прозрачным фоном и размером не более
                        200х200.
                        Рекомендуем использовать SVG-файлы
                    </div>

                    <div>Для типа <b>варианты</b> к каждому значению варианта атрибута предусмотрена возможность
                        установления
                        изображения, например для цвета. Привязать изображение к варианту можно после сохранения
                        атрибута в режиме просмотра.
                    </div>
                </div>
            </div>
        </el-col>
    </el-row>
</template>

<script lang="ts" setup>
import {func} from '@Res/func.js'
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import Active from "@Comp/Elements/Active.vue";
import VarianField from "@Page/Product/Attribute/Block/VarianField.vue";

const props = defineProps({
    attribute: Object,
    categories: Array,
    groups: Array,
    types: Array,
    variant: Number,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.attribute.name,
    categories: [...props.attribute.categories.map(item => item.id)],
    group_id: props.attribute.group_id,
    filter: props.attribute.filter,
    multiple: props.attribute.multiple,
    show_in: props.attribute.show_in,
    sameAs: props.attribute.sameAs,
    type: props.attribute.type,

    file: null,
    clear_file: false,
    variants: null,
})
const showEdit = ref(false)

function onSetInfo() {
    if (info.type === props.variant) info.variants = Variants.value

    router.visit(
        route('admin.product.attribute.set-info', {attribute: props.attribute.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

function onSelectImage(val) {
    info.clear_file = val.clear_file;
    info.file = val.file
}

//Варианты
interface IVariantData {
    id: Number,
    name: String,
    image: String,
    file: Object,
    clear_file: Boolean,
    identity: String,
}

const Variants = ref<IVariantData[]>([]);

if (props.attribute.is_variant) {
    props.attribute.variants.forEach(function (item) {
        Variants.value.push({
            id: item.id,
            name: item.name,
            image: item.image,
            file: null,
            clear_file: false,
            identity: Math.random().toString(36).slice(2),
        })
    })
    console.log(props.attribute.variants)
}

function addVariant() {
    Variants.value.push({
        id: null,
        name: null,
        image: null,
        file: null,
        clear_file: false,
        identity: Math.random().toString(36).slice(2),
    })
    // console.log(Variants.value)
}

function onUpdateVariant(val, identity) {
    Variants.value.forEach(function (item) {
        if (item.identity === identity) {
            item.name = val.name
            item.file = val.file
            item.clear_file = val.clear_file
        }
    })

}

function onRemoveVariant(identity) {
    let index = Variants.value.map(function (el) {
        return el.identity;
    }).indexOf(identity);

    Variants.value.splice(index, 1)
    // console.log(Variants.value)
}
</script>
