<template>
    <el-row :gutter="10" v-if="!editBanner">
        <el-col :span="6">
            <el-tooltip content="Изображение" placement="top-start" effect="dark">
                <el-image
                    style="width: 200px; height: 200px"
                    :src="banner.image"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[banner.image]"
                    fit="cover"
                />
            </el-tooltip>
            <el-tooltip content="Иконка" placement="top-start" effect="dark">
                <el-image
                    style="width: 100px; height: 100px"
                    :src="banner.icon"
                    :zoom-rate="1.2"
                    :max-scale="7"
                    :min-scale="0.2"
                    :initial-index="4"
                    :preview-src-list="[banner.icon]"
                    fit="cover"
                    class="ml-3"
                />
            </el-tooltip>
        </el-col>
        <el-col :span="12">
    <el-descriptions :column="3" border class="mb-5">
        <el-descriptions-item label="Баннер">
            {{ banner.name }}
        </el-descriptions-item>
        <el-descriptions-item label="Шаблон">
            {{ banner.template }}
        </el-descriptions-item>
        <el-descriptions-item label="Заголовок">
            {{ banner.caption }}
        </el-descriptions-item>
        <el-descriptions-item label="Описание">
            {{ banner.description }}
        </el-descriptions-item>
    </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!editBanner" type="warning" @click="editBanner = true">Изменить</el-button>
    <el-row :gutter="10" v-if="editBanner">
        <el-col :span="8">
            <el-form label-width="auto" >
        <el-form-item label="Баннер">
            <el-input v-model="form.name" />
        </el-form-item>
        <el-form-item label="Шаблон">
            <el-select v-model="form.template">
                <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
        </el-form-item>
        <el-form-item label="Заголовок">
            <el-input v-model="form.caption" />
        </el-form-item>
        <el-form-item label="Описание">
            <el-input v-model="form.description" type="textarea" rows="3"/>
        </el-form-item>

        <el-button type="info" @click="editBanner = false">Отмена</el-button>
        <el-button type="success" @click="setBanner">Сохранить</el-button>
    </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение"
                v-model:image="banner.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка"
                v-model:image="banner.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from "@Comp/UploadImageFile.vue";

const props = defineProps({
    banner: Object,
    templates: Array,
})

const editBanner = ref(false)
const form = reactive({
    name: props.banner.name,
    template: props.banner.template,

    caption: props.banner.caption,
    description: props.banner.description,
    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,
})


function setBanner() {
    router.visit(route('admin.page.widget.banner.set-banner', {banner: props.banner.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editBanner.value = false;
        }
    })
}
function onSelectImage(val) {
    form.clear_image = val.clear_file;
    form.image = val.file
}

function onSelectIcon(val) {
    form.clear_icon = val.clear_file;
    form.icon = val.file
}
</script>
