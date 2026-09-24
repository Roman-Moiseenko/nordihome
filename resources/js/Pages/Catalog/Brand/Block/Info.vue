<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="4">
                <el-tooltip content="Изображение для карточек" placement="top-start" effect="dark">
                    <PhotoDTO model-type="catalog.brand" :entity-id="brand.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Название бренда">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка на сайт">
                    <el-input v-model="info.url" clearable placeholder="https://"/>
                </el-form-item>
                <el-form-item label="Валюта парсера">
                    <el-select v-model="info.currency_id" clearable placeholder="Валюта">
                        <el-option v-for="item in currencies" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="10">
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
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

<script setup>
import {reactive, computed} from "vue";
import {router} from "@inertiajs/vue3";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    brand: Object,
    parsers: Array,
    currencies: Array,
})

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.brand.name,
    url: props.brand.url ?? '',
    currency_id: props.brand.currency_id ?? null,
    description: props.brand.description ?? '',
    sameAs: props.brand.sameAs ?? '',
}

const info = reactive({...initialInfo})

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        const a = JSON.stringify(info[key])
        const b = JSON.stringify(initialInfo[key])
        if (a !== b) return true
    }
    return false
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    router.visit(
        route('admin.catalog.brand.set-info', {brand: props.brand.id}), {
            method: "post",
            data: {...info},
            onSuccess: page => {
                Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
            }
        }
    );
}

</script>

<style scoped>

</style>
