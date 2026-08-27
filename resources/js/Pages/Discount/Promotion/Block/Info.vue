<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="6">
            <PhotoDTO model-type="discount.promotion" :entity-id="promotion.id" type="image"/>
            <el-tooltip content="Изображение для карточек" placement="top-start" effect="dark">
            </el-tooltip>
        </el-col>
        <el-col :span="12">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Внутреннее имя">
                    {{ promotion.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ promotion.slug }}
                </el-descriptions-item>

                <el-descriptions-item label="Заголовок для клиента">
                    {{ promotion.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка на условия акции">
                    {{ promotion.condition_url }}
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ promotion.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="6">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Показывать в меню">
                    <Active :active="promotion.menu"/>
                </el-descriptions-item>
                <el-descriptions-item label="Показывать заголовок">
                    <Active :active="promotion.show_title"/>
                </el-descriptions-item>
                <el-descriptions-item label="Базовая скидка">
                    {{ promotion.discount }}%
                </el-descriptions-item>
                <el-descriptions-item label="Начало акции">
                    {{ func.date(promotion.start_at) }}
                </el-descriptions-item>
                <el-descriptions-item label="Окончание акции">
                    {{ func.date(promotion.finish_at) }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!showEdit" type="warning" @click="showEdit = true">
        <i class="fa-light fa-pen-to-square"></i>&nbsp;Редактировать
    </el-button>
    <el-form label-width="auto">
        <el-row :gutter="10" v-if="showEdit">
            <el-col :span="8">

                <el-form-item label="Внутреннее имя">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug"/>
                </el-form-item>
                <el-form-item label="Показывать в меню">
                    <el-checkbox v-model="info.menu" :checked="info.menu"/>
                </el-form-item>
                <el-form-item label="Заголовок для клиента">
                    <el-input v-model="info.title"/>
                </el-form-item>
                <el-form-item label="Базовая скидка">
                    <el-input v-model="info.discount" :formatter="val => func.MaskInteger(val)">
                        <template #append>%</template>
                    </el-input>
                </el-form-item>
                <el-form-item label="Показывать заголовок">
                    <el-checkbox v-model="info.show_title" :checked="info.show_title"/>
                </el-form-item>
                <el-form-item label="Ссылка на условия акции">
                    <el-input v-model="info.condition_url"/>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
                </el-form-item>
                <el-form-item label="Начало акции">
                    <el-date-picker v-model="info.start_at" type="date" placeholder="Ручной запуск" clearable/>
                </el-form-item>

                <el-form-item label="Конец акции">
                    <el-date-picker v-model="info.finish_at" type="date" placeholder="Бессрочная акция" clearable/>
                </el-form-item>

            </el-col>
            <el-col :span="8">
                <el-divider >Метка на карточке</el-divider>

            </el-col>
            <el-col :span="8">

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

<script setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import HelpBlock from "@Comp/HelpBlock.vue";
import Active from "@Comp/Elements/Active.vue";
import {func} from "@Res/func.js"
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    promotion: Object,
})

const iSavingInfo = ref(false)
const info = reactive({
    name: props.promotion.name,
    title: props.promotion.title,
    description: props.promotion.description,
    slug: props.promotion.slug,
    menu: props.promotion.menu,
    show_title: props.promotion.show_title,
    condition_url: props.promotion.condition_url,
    discount: props.promotion.discount,
    start_at: props.promotion.start_at,
    finish_at: props.promotion.finish_at,

})
const showEdit = ref(false)

function onSetInfo() {
    if (info.start_at) info.start_at = func.date(info.start_at)
    if (info.finish_at) info.finish_at = func.date(info.finish_at)
    router.visit(
        route('admin.discount.promotion.set-info', {id: props.promotion.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

</script>
