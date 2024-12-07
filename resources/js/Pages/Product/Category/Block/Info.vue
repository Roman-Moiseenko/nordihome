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
                    <el-input v-model="info.slug"/>
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
            <div class="bg-warning/20 border border-warning rounded-md relative p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"

                     class=" w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3">
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
                    <div><b>Название категории</b> является обязательным полем.</div>
                    <div class="mt-2">Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</div>
                    <div class="mt-2">Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</div>
                    <div class="mt-2"><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg. Разрешение не более 200х200.</div>
                    <div class="mt-2">Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их пустыми.</div>
                </div>
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'


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
    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,
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
