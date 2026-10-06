<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="4">
                <el-tooltip content="Изображение атрибута" placement="top-start" effect="dark">
                    <PhotoDTO model-type="catalog.attribute" :entity-id="attribute.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="14">
                <el-form-item label="Название атрибута" label-position="top">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Категория" label-position="top">
                    <el-select v-model="info.categories" filterable multiple>
                        <el-option v-for="item in useCatalog.categories" :key="item.id" :value="item.id"
                                   :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Группа" label-position="top">
                    <el-select v-model="info.group_id" filterable>
                        <el-option v-for="item in useCatalog.attrGroups" :key="item.id" :value="item.id"
                                   :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Тип значения атрибута" label-position="top">
                    <el-select v-model="info.type">
                        <el-option v-for="item in useCatalog.attrTypes" :key="item.value" :value="item.value"
                                   :label="item.label"/>
                    </el-select>
                </el-form-item>

                <div v-if="info.type === variantType" class="mb-5">
                    <h2>Варианты</h2>
                    <VariantField
                        v-for="item in info.variants" :key="item.identity"
                        :id="item.id"
                        :name="item.name"
                        @update:fields="val => onUpdateVariant(val, item.identity)"
                        @remove:fields="onRemoveVariant(item.identity)"
                    />
                    <el-button @click="addVariant">Добавить вариант</el-button>
                </div>
            </el-col>
            <el-col :span="6">
                <el-form-item label="Множественный выбор">
                    <el-checkbox v-model="info.multiple"/>
                </el-form-item>
                <el-form-item label="Используется для фильтрации">
                    <el-checkbox v-model="info.filter"/>
                </el-form-item>
                <el-form-item label="Показывать в поиске и описании">
                    <el-checkbox v-model="info.show_in"/>
                </el-form-item>
                <el-form-item label="Ссылка на википедию" label-position="top">
                    <el-input v-model="info.sameAs"/>
                </el-form-item>
            </el-col>
        </el-row>
        <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
            Отмена
        </el-button>
        <el-button v-if="hasChanges" type="success" @click="onSetInfo">
            Сохранить
        </el-button>
    </el-form>
</template>

<script setup lang="ts">
import {reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";
import VariantField from "./VarianField.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import {route} from "ziggy-js";

const useCatalog = useCatalogStore()

const props = defineProps({
    attribute: Object,
})

const variantType = useCatalog.attrTypes.find(t => t.isVariant)?.value

interface IVariant {
    id: number | null,
    name: string | null,
    identity: string,
}

function makeVariant(id: number | null, name: string | null): IVariant {
    return {
        id: id ?? null,
        name: name ?? null,
        identity: Math.random().toString(36).slice(2),
    }
}

// --- Исходные данные из пропсов (эталон для отмены и сравнения) ---
const initialInfo = {
    name: props.attribute.name ?? '',
    categories: [...((props.attribute.categories ?? []).map(item => item.id))],
    group_id: props.attribute.group_id ?? null,
    type: props.attribute.type,
    multiple: !!props.attribute.multiple,
    filter: !!props.attribute.filter,
    show_in: !!props.attribute.show_in,
    sameAs: props.attribute.sameAs ?? '',
    variants: props.attribute.is_variant
        ? (props.attribute.variants ?? []).map(item => makeVariant(item.id, item.name))
        : [],
}

const info = reactive({
    name: initialInfo.name,
    categories: [...initialInfo.categories],
    group_id: initialInfo.group_id,
    type: initialInfo.type,
    multiple: initialInfo.multiple,
    filter: initialInfo.filter,
    show_in: initialInfo.show_in,
    sameAs: initialInfo.sameAs,
    variants: initialInfo.variants.map(v => ({...v})),
})

// --- Отслеживание изменений ---
function variantsKey(variants: IVariant[]): string {
    return variants.map(v => `${v.id ?? ''}|${v.name ?? ''}`).join(',')
}

const hasChanges = computed(() => {
    return ['name', 'group_id', 'type', 'multiple', 'filter', 'show_in', 'sameAs'].some(
        key => JSON.stringify(info[key]) !== JSON.stringify(initialInfo[key])
    )
        || JSON.stringify(info.categories) !== JSON.stringify(initialInfo.categories)
        || variantsKey(info.variants) !== variantsKey(initialInfo.variants)
})

function onCancel() {
    info.name = initialInfo.name
    info.categories = [...initialInfo.categories]
    info.group_id = initialInfo.group_id
    info.type = initialInfo.type
    info.multiple = initialInfo.multiple
    info.filter = initialInfo.filter
    info.show_in = initialInfo.show_in
    info.sameAs = initialInfo.sameAs
    info.variants = initialInfo.variants.map(v => ({...v}))
}

function onSetInfo() {
    router.visit(
        route('admin.catalog.attribute.update', {id: props.attribute.id}), {
            method: "put",
            data: {
                name: info.name,
                type: info.type,
                group_id: info.group_id,
                multiple: info.multiple,
                filter: info.filter,
                show_in: info.show_in,
                sameAs: info.sameAs,
                categories: info.categories,
                variants: info.type === variantType
                    ? info.variants.map(v => ({id: v.id, name: v.name}))
                    : null,
            },
            preserveState: false,
            preserveScroll: true,
        }
    );
}

// --- Варианты ---
function addVariant() {
    info.variants.push(makeVariant(null, null))
}

function onUpdateVariant(val, identity) {
    info.variants.forEach(function (item) {
        if (item.identity === identity) {
            item.name = val.name
        }
    })
}

function onRemoveVariant(identity) {
    const index = info.variants.map(el => el.identity).indexOf(identity)
    if (index !== -1) info.variants.splice(index, 1)
}
</script>

<style scoped>
</style>
