<template>
    <el-row :gutter="10" v-if="!showEdit">
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
        <el-col :span="12">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Категория">
                    {{ category.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ category.slug }}
                </el-descriptions-item>
                <el-descriptions-item label="SVG">
                    <span v-html="category.svg" class="svg-category"></span>
                </el-descriptions-item>
                <el-descriptions-item label="Meta-Title">
                    {{ category.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Meta-Description">
                    {{ category.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>

    </el-row>

    <el-button v-if="!showEdit" class="ml-2" type="warning" @click="showEdit = true">
        <i class="fa-light fa-pen-to-square"></i>&nbsp;Редактировать
    </el-button>

    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Родительская категория">
                    <el-select v-model="info.parent_id">
                        <el-option v-for="item in categories" :key="item.id" :value="item.id" :label="item.name" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Название категории">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable/>
                </el-form-item>
                <el-form-item label="SVG">
                    <el-input v-model="info.svg" clearable type="textarea" :rows="3"/>
                </el-form-item>
                <el-form-item label="Meta-Title">
                    <el-input v-model="info.title" />
                </el-form-item>
                <el-form-item label="Meta-Description">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
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
                <p><b>Название категории</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg. Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их пустыми.</p>
            </HelpBlock>
        </el-col>
    </el-row>
    <el-row :gutter="10" v-if="showEdit" class="mt-2">
        <el-col :span="6">
            <h2>Данные перед списком товаров</h2>
            <el-form-item label="Заголовок">
                <el-input v-model="info.top_title"/>
            </el-form-item>
            <el-form-item label="Текст">
                <el-input v-model="info.top_description" type="textarea" rows="5"/>
            </el-form-item>
        </el-col>
        <el-col :span="12">
            <h2>Данные после списка товаров</h2>

                <el-input v-model="info.bottom_text" type="textarea" rows="10"/>

        </el-col>

        <el-col :span="6">
            <h2>Скрытые данные</h2>

                <el-input v-model="info.data" type="textarea" rows="10"/>

        </el-col>
    </el-row>
</template>

<script setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import HelpBlock from "@Comp/HelpBlock.vue";


const props = defineProps({
    category: Object,
    categories: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.category.name,
    title: props.category.title,
    description: props.category.description,
    slug: props.category.slug,
    parent_id: props.category.parent_id,
    svg: props.category.svg,
    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,

    top_title: props.category.top_title,
    top_description: props.category.top_description,
    bottom_text: props.category.bottom_text,
    data: props.category.data,


})
const showEdit = ref(false)

function onSetInfo() {
    router.visit(
        route('admin.product.category.set-info', {category: props.category.id}), {
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

<style scoped>
    span.svg-category::v-deep>svg {
        max-height: 50px;
    }
</style>
