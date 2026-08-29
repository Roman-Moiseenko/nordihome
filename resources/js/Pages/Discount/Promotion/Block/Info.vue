<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="3">
                <el-tooltip content="Изображение для карточек" placement="top-start" effect="dark">
                    <PhotoDTO model-type="discount.promotion" :entity-id="promotion.id" type="image"/>
                </el-tooltip>
            </el-col>
            <el-col :span="7">
                <el-form-item label="Название акции">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable placeholder="Slug"/>
                </el-form-item>

                <el-form-item label="Ссылка на условия акции">
                    <el-input v-model="info.conditionUrl"/>
                </el-form-item>
                <el-form-item label="Базовая скидка">
                    <el-input v-model="info.discount" :formatter="val => func.MaskInteger(val)">
                        <template #append>%</template>
                    </el-input>
                </el-form-item>
                <el-form-item label="meta Title">
                    <el-input v-model="info.metaTitle"/>
                </el-form-item>
                <el-form-item label="meta Description">
                    <el-input v-model="info.metaDescription" type="textarea" :rows="1"/>
                </el-form-item>
            </el-col>
            <el-col :span="7">
                <el-form-item label="Начало акции">
                    <el-date-picker v-model="info.startAt" type="date" value-format="YYYY-MM-DD" placeholder="Ручной запуск" clearable/>
                </el-form-item>
                <el-form-item label="Конец акции">
                    <el-date-picker v-model="info.finishAt" type="date" value-format="YYYY-MM-DD" placeholder="Бессрочная акция" clearable/>
                </el-form-item>
                <el-form-item label="Показывать в меню">
                    <el-switch v-model="info.menu"/>
                </el-form-item>
                <el-form-item label="Показывать заголовок">
                    <el-switch v-model="info.showTitle"/>
                </el-form-item>
                <el-form-item label="SVG">
                    <el-input v-model="info.svg" type="textarea" :rows="1"/>
                </el-form-item>
            </el-col>
            <el-col :span="7">
                <el-divider>Метка на карточке</el-divider>
                <el-form-item label="Цвет">
                    <el-select v-model="info.colorClass">
                        <el-option v-for="color in colors" :key="color" :label="color" :value="color"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Позиция">
                    <el-select v-model="info.positionClass">
                        <el-option v-for="position in positions" :key="position" :label="position" :value="position"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Текст">
                    <el-input v-model="info.textTag"/>
                </el-form-item>
                <el-form-item label="Показывать метку">
                    <el-switch v-model="info.showTag"/>
                </el-form-item>
                <el-form-item label="Показывать скидку">
                    <el-switch v-model="info.showDiscount"/>
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
import {func} from '@Res/func.js'
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    promotion: Object,
})

const colors = ['red', 'green', 'yellow', 'gray', 'black']
const positions = ['top-left', 'top-right', 'bottom-left', 'bottom-right']

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.promotion.name,
    slug: props.promotion.slug ?? '',
    metaTitle: props.category?.meta?.title ?? '',
    metaDescription: props.category?.meta?.description ?? '',
    conditionUrl: props.promotion.conditionUrl ?? '',
    menu: !!props.promotion.menu,
    showTitle: !!props.promotion.showTitle,
    discount: props.promotion.discount ?? 0,
    published: !!props.promotion.published,
    active: !!props.promotion.active,
    startAt: props.promotion.startAt ? func.date(props.promotion.startAt) : null,
    finishAt: props.promotion.finishAt ? func.date(props.promotion.finishAt) : null,
    colorClass: props.promotion.colorClass ?? 'red',
    positionClass: props.promotion.positionClass ?? 'top-right',
    textTag: props.promotion.textTag ?? 'Акция',
    showTag: !!props.promotion.showTag,
    showDiscount: !!props.promotion.showDiscount,
    svg: props.promotion.svg ?? null,
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
        route('admin.discount.promotion.set-info', {id: props.promotion.id}), {
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
