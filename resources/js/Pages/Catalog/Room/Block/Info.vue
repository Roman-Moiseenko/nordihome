айл<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="6">
            <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.room" :entity-id="room.id" type="image" />

            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.room" :entity-id="room.id" type="icon" />
            </el-tooltip>
        </el-col>
        <el-col :span="12">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Комната">
                    {{ room.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ room.slug }}
                </el-descriptions-item>
                <el-descriptions-item label="SVG">
                    <span v-html="room.svg" class="svg-category"></span>
                </el-descriptions-item>
                <el-descriptions-item label="Meta-Title">
                    {{ room.title }}
                </el-descriptions-item>
                <el-descriptions-item label="Meta-Description">
                    {{ room.description }}
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
                <el-form-item label="Родительская комната">
                    <el-select v-model="info.parentId">
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
                v-model:image="room.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка для меню"
                v-model:image="room.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название комнаты</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg. Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их пустыми.</p>
            </HelpBlock>
        </el-col>
    </el-row>

</template>

<script setup>
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import HelpBlock from "@Comp/HelpBlock.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";


const props = defineProps({
    room: Object,
    categories: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.room.name,
    title: props.room.title,
    description: props.room.description,
    slug: props.room.slug,
    parentId: props.room.parentId,
    svg: props.room.svg,




})
const showEdit = ref(false)

function onSetInfo() {
    router.visit(
        route('admin.catalog.room.update', {room: props.room.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

</script>

<style scoped>
    span.svg-category::v-deep>svg {
        max-height: 50px;
    }
</style>
