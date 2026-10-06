<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="4">
            <PhotoDTO model-type="catalog.attribute" :entity-id="attribute.id"/>
        </el-col>
        <el-col :span="20">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Группа">
                    {{ attribute.group }}
                </el-descriptions-item>
                <el-descriptions-item label="Категории">
                    <el-tag v-for="item in attribute.categories" class="ml-1">{{ item.name }}</el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Тип">
                    {{ attribute.type_text }}
                    <span class="flex">
                    <div v-if="attribute.is_variant" v-for="item in attribute.variants" class="flex mb-1">

                        <el-tag type="info" class="my-auto ml-1">{{ item.name }}</el-tag>

                        <el-image v-if="item.image && item.image !== 'images/no-image.jpg'"
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
                        </span>
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
    <el-form label-width="auto" v-if="showEdit">
        <el-row :gutter="10">

            <el-col :span="10">

                <el-form-item label="Название атрибута">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Категория">
                    <el-select v-model="info.categories" filterable multiple>
                        <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id"
                                   :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Группа">
                    <el-select v-model="info.group_id" filterable>
                        <el-option v-for="item in useCatalog.attrGroups" :key="item.id" :value="item.id"
                                   :label="item.name"/>
                    </el-select>
                </el-form-item>


                <el-form-item label="Тип значения атрибута ">
                    <el-select v-model="info.type">
                        <el-option v-for="item in useCatalog.attrTypes" :key="item.value" :value="item.value"
                                   :label="item.label"/>
                    </el-select>
                </el-form-item>

                <div v-if="info.type === variantType" class="mb-5">
                    <h2>Варианты</h2>
                    <VariantField
                        v-for="item in Variants" :key="item"
                        :id="item.id"
                        :name="item.name"
                        @update:fields="val => onUpdateVariant(val, item.identity)"
                        @remove:fields="onRemoveVariant(item.identity)"
                    />
                    <el-button @click="addVariant">Добавить вариант</el-button>
                </div>

            </el-col>
            <el-col :span="10">
                <el-form-item label="Множественный выбор">
                    <el-checkbox v-model="info.multiple" :checked="info.multiple"/>
                </el-form-item>
                <el-form-item label="Используется для фильтрации">
                    <el-checkbox v-model="info.filter" :checked="info.filter"/>
                </el-form-item>
                <el-form-item label="Показывать в поиске и описании">
                    <el-checkbox v-model="info.show_in" :checked="info.show_in"/>
                </el-form-item>
                <el-form-item label="Ссылка на википедию">
                    <el-input v-model="info.sameAs"/>
                </el-form-item>
            </el-col>

        </el-row>
        <el-button type="info" @click="showEdit = false" style="margin-left: 4px">
            Отмена
        </el-button>
        <el-button type="success" @click="onSetInfo">
            Сохранить
        </el-button>
    </el-form>

</template>

<script lang="ts" setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import VariantField from "./VarianField.vue";
import HelpBlock from "@Comp/HelpBlock.vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import PhotoDTO from "@Comp/PhotoDTO.vue";
import {route} from "ziggy-js";

const useCatalog = useCatalogStore()

const props = defineProps({
    attribute: Object,
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

    variants: null,
})
const showEdit = ref(false)
const variantType = useCatalog.attrTypes.find(t => t.isVariant)?.value

function onSetInfo() {
    if (info.type === variantType) info.variants = Variants.value

    router.visit(
        route('admin.catalog.attribute.update', {id: props.attribute.id}), {
            method: "put",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

//Варианты
interface IVariantData {
    id: Number,
    name: String,
    image: String,
    identity: String,
}

const Variants = ref<IVariantData[]>([]);

if (props.attribute.is_variant) {
    props.attribute.variants.forEach(function (item) {
        Variants.value.push({
            id: item.id,
            name: item.name,
            image: item.image,
            identity: Math.random().toString(36).slice(2),
        })
    })
}

function addVariant() {
    Variants.value.push({
        id: null,
        name: null,
        image: null,
        identity: Math.random().toString(36).slice(2),
    })
    // console.log(Variants.value)
}

function onUpdateVariant(val, identity) {
    Variants.value.forEach(function (item) {
        if (item.identity === identity) {
            item.name = val.name
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
