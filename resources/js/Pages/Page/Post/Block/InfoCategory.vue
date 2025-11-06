<template>
    <el-row :gutter="10" v-if="!editInfo">
        <el-col :span="6">
            <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                <el-image
                    style="width: 200px; height: 200px"
                    :src="category.image"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[category.image]"
                    fit="cover"
                />
            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <el-image
                    style="width: 100px; height: 100px"
                    :src="category.icon"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[category.icon]"
                    fit="cover"
                    class="ml-3"
                />
            </el-tooltip>
        </el-col>
        <el-col :span="18">
            <el-descriptions v-if="!editInfo" :column="2" border class="mb-5">
                <el-descriptions-item label="Страница">
                    {{ category.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ category.slug }}
                </el-descriptions-item>
                <el-descriptions-item label="Заголовок">
                    {{ category.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ category.description }}
                </el-descriptions-item>
                <el-descriptions-item label="Шаблон">
                    {{ category.template }}
                </el-descriptions-item>
                <el-descriptions-item label="Записей на странице">
                    {{ category.paginate }}
                </el-descriptions-item>
                <el-descriptions-item label="Шаблон для записей">
                    {{ category.post_template }}
                </el-descriptions-item>
                <el-descriptions-item label="Мета Заголовок">
                    {{ category.meta.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Мета Описание">
                    {{ category.meta.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!editInfo" type="warning" @click="editInfo = true">Изменить</el-button>
    <el-row v-if="editInfo" :gutter="10">
        <el-col :span="8">
            <el-form label-width="auto">

                <el-form-item label="Название рубрики">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable placeholder="Slug"/>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="info.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Записей на странице">
                    <el-input v-model="info.paginate"/>
                </el-form-item>
                <el-form-item label="Шаблон для записей" >
                    <el-select v-model="info.post_template" clearable  placeholder="По-умолчанию">
                        <el-option v-for="item in post_templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Заголовок">
                    <el-input v-model="info.title"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
                </el-form-item>
                <el-form-item label="Meta-Title">
                    <el-input v-model="info.meta_title"/>
                </el-form-item>
                <el-form-item label="Meta-Description">
                    <el-input v-model="info.meta_description" type="textarea" :rows="5"/>
                </el-form-item>

                <el-button type="info" @click="editInfo = false" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение для каталога"
                v-model:image="category.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка для меню"
                v-model:image="category.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название рубрики</b> является обязательным полем.</p>
                <p><b>Шаблон записи</b> - шаблон по-умолчанию, для каждой записи можно выбрать свой шаблон.</p>
                <p>Поле <b>Slug</b> (ссылка на рубрику) можно не заполнять, тогда оно заполнится автоматически. При
                    заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg.
                    Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для заполнения обязательны.</p>
            </HelpBlock>
        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import {ref, defineProps, reactive} from "vue";
import {router} from "@inertiajs/vue3";
import {ISelectItem} from '@Res/interface.d.ts'

import UploadImageFile from "@Comp/UploadImageFile.vue";
import HelpBlock from "@Comp/HelpBlock.vue";

const editInfo = ref(false)
const props = defineProps({
    category: Object,
    templates: Array<ISelectItem>,
    post_templates: Array<ISelectItem>,
})
const info = reactive({
    name: props.category.name,
    template: props.category.template,
    post_template: props.category.post_template,
    title: props.category.title,
    description: props.category.description,
    slug: props.category.slug,
    paginate: props.category.paginate,

    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,

    meta_title: props.category.meta.title,
    meta_description: props.category.meta.description,
})

function onSetInfo() {
    router.visit(
        route('admin.page.post-category.set-info', {category: props.category.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                editInfo.value = false;
            }
        }
    );
}

function onSelectImage(val: any) {
    info.clear_image = val.clear_file
    info.image = val.file
}

function onSelectIcon(val: any) {
    info.clear_icon = val.clear_file
    info.icon = val.file
}
</script>
<style scoped>

</style>
