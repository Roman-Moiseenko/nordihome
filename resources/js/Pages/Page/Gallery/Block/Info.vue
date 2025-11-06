<template>
    <el-row :gutter="10" v-if="!editInfo">
        <el-col :span="10">
            <el-descriptions :column="1" border>
                <el-descriptions-item label="Галерея">{{ gallery.name }}</el-descriptions-item>
                <el-descriptions-item label="Ссылка">{{ gallery.slug }}</el-descriptions-item>
                <el-descriptions-item label="Описание">{{ gallery.description }}</el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="4">
        </el-col>
        <el-col :span="10">
            <HelpBlock>

                <p>В шаблоне страниц и виджетов используйте функцию <strong>photo(id, thumb)</strong> для получения ссылки</p>
                <p>Для получения полных данных -  <strong>photo_std(id, thumb)</strong> возвращает объект stdClass {url, alt, title, description} </p>
                <p><b>id</b> - идентификатор изображения (число) или ссылка (slug - строка), thumb - вид карточки (mini, thumb, card и другие, определенные в
                    <Link type="primary" :href="route('admin.setting.image', undefined, false)"> Настройки > Изображения) </Link></p>
            </HelpBlock>
        </el-col>
    </el-row>
    <el-button v-if="!editInfo" type="warning" @click="editInfo = true">Изменить</el-button>
    <el-row v-if="editInfo" :gutter="10">
        <el-col :span="10">
            <el-form :model="form" label-width="auto" style="max-width: 500px">
                <el-form-item label="Название" :rules="{required: true}">
                    <el-input v-model="form.name" placeholder="Название"/>
                    <div v-if="errors.name" class="text-red-700">{{ errors.name }}</div>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="form.slug" placeholder="Оставьте пустым для заполнения" :formatter="(val) => func.MaskSlug(val)"/>
                    <div v-if="errors.slug" class="text-red-700">{{ errors.slug }}</div>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="form.description" placeholder="Описание" :rows="5" type="textarea" maxlength="250" show-word-limit/>
                    <div v-if="errors.description" class="text-red-700">{{ errors.description }}</div>
                </el-form-item>
                <el-button type="info" @click="editInfo = false">Отмена</el-button>
                <el-button type="success" @click="setInfo">Сохранить</el-button>

            </el-form>
        </el-col>
    </el-row>

</template>

<script setup lang="ts">
import HelpBlock from "@Comp/HelpBlock.vue";
import {ref, reactive} from "vue";
import { func } from "@Res/func.js"
import {router} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    gallery: Object,
    errors: Object,
});

const editInfo = ref(false)
const form = reactive({
    name: props.gallery.name,
    slug: props.gallery.slug,
    description: props.gallery.description,
})
function setInfo() {
    router.visit(route('admin.page.gallery.set-info', {gallery: props.gallery.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editInfo.value = false;
        }
    })
}

</script>
