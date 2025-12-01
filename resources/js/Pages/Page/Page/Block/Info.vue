<template>
    <el-row :gutter="10" v-if="!editPage">
        <el-col :span="6">
            <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                <el-image
                    style="width: 200px; height: 200px"
                    :src="page.image"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[page.image]"
                    fit="cover"
                />
            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <el-image
                    style="width: 100px; height: 100px"
                    :src="page.icon"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[page.icon]"
                    fit="cover"
                    class="ml-3"
                />
            </el-tooltip>
        </el-col>
        <el-col :span="18">
            <el-descriptions v-if="!editPage" :column="3" border class="mb-5">
                <el-descriptions-item label="Страница">
                    {{ page.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ page.slug }}
                </el-descriptions-item>
                <el-descriptions-item label="Родительская">
                    {{ page.parent_name }}
                </el-descriptions-item>
                <el-descriptions-item label="Шаблон">
                    {{ page.template }}
                </el-descriptions-item>
                <el-descriptions-item label="Заголовок">
                    {{ page.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ page.description }}
                </el-descriptions-item>
                <el-descriptions-item label="Мета Заголовок">
                    {{ page.meta.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Мета Описание">
                    {{ page.meta.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!editPage" type="warning" @click="editPage = true">Изменить</el-button>

    <el-link v-if="!editPage" type="info" :underline="false" class="ml-2"
             :href="route('shop.page.view', {slug: page.slug})"
             target="_blank">
        Просмотр
    </el-link>

    <el-row v-if="editPage" :gutter="10">
        <el-col :span="8">
            <el-form v-if="editPage" label-width="auto">
                <el-form-item label="Страница">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="form.slug" clearable/>
                </el-form-item>
                <el-form-item label="Родительская">
                    <el-select v-model="form.parent_id" clearable filterable>
                        <el-option v-for="item in pages" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="form.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Заголовок">
                    <el-input v-model="form.title"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="form.description" type="textarea" rows="3"/>
                </el-form-item>
                <el-form-item label="Мета Заголовок">
                    <el-input v-model="form.meta_title"/>
                </el-form-item>
                <el-form-item label="Мета Описание">
                    <el-input v-model="form.meta_description" type="textarea" rows="3"/>
                </el-form-item>

                <el-button type="info" @click="editPage = false">Отмена</el-button>
                <el-button type="success" @click="setPage">Сохранить</el-button>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение для каталога"
                v-model:image="page.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка для меню"
                v-model:image="page.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название страницы</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на страницу) можно не заполнять, тогда оно заполнится автоматически. При
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
/*
scope.row.published
             ? route('shop.product.view', {slug: scope.row.slug})
             : route('shop.product.view-draft', {product: scope.row.id})
 */

import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";

import {ISelectItem} from '@Res/interface.d.ts'
import UploadImageFile from "@Comp/UploadImageFile.vue";
import HelpBlock from "@Comp/HelpBlock.vue";
import {route} from "ziggy-js";

const props = defineProps({
    page: Object,
    templates: Array<ISelectItem>,
    pages: Array,
})

const editPage = ref(false)
const form = reactive({
    name: props.page.name,
    slug: props.page.slug,
    parent_id: props.page.parent_id,
    template: props.page.template,
    title: props.page.title,
    description: props.page.description,

    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,

    meta_title: props.page.meta.title,
    meta_description: props.page.meta.description,
})


function setPage() {
    router.visit(route('admin.page.page.set-info', {page: props.page.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editPage.value = false;
        }
    })
}

function onSelectImage(val: any) {
    form.clear_image = val.clear_file
    form.image = val.file
}

function onSelectIcon(val: any) {
    form.clear_icon = val.clear_file
    form.icon = val.file
}

function onShow() {

}
</script>
