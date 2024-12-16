<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="6">
            <el-tooltip content="Изображение для карточек" placement="top-start" effect="dark">
                <el-image
                    style="width: 200px; height: 200px"
                    :src="promotion.image"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[promotion.image]"
                    fit="cover"
                />
            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <el-image
                    style="width: 100px; height: 100px"
                    :src="promotion.icon"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[promotion.icon]"
                    fit="cover"
                    class="ml-3"
                />
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
    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="8">
            <el-form label-width="auto">
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

                <el-button type="info" @click="showEdit = false" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение для карточки"
                v-model:image="promotion.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка для меню"
                v-model:image="promotion.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p>Параметры <b>Начало акции</b> и <b>Конец акции</b> нужны для автоматического запуска и завершения
                    акции.</p>
                <p>В ином случае акцию можно запускать в ручную.</p>
                <p><b>Базовая скидка</b> используется для автоматического расчета стоимости товара при его добавлении в
                    акцию. Далее, для каждого товара из акции можно вручную задать цену.</p>
                <p>При изменении <b>базовой скидки</b> будет произведен перерасчет стоимости уже добавленных товаров</p>
            </HelpBlock>
        </el-col>
    </el-row>
</template>

<script setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import HelpBlock from "@Comp/HelpBlock.vue";
import Active from "@Comp/Elements/Active.vue";
import {func} from "@Res/func.js"

const props = defineProps({
    promotion: Object,
})
console.log(props.promotion)
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

    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,
})
const showEdit = ref(false)

function onSetInfo() {
    if (info.start_at) info.start_at = func.date(info.start_at)
    if (info.finish_at) info.finish_at = func.date(info.finish_at)
    router.visit(
        route('admin.discount.promotion.set-info', {promotion: props.promotion.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

function onSelectImage(val) {
    info.clear_image = val.clear_file;
    info.image = val.file
}

function onSelectIcon(val) {
    info.clear_icon = val.clear_file;
    info.icon = val.file
}
</script>
