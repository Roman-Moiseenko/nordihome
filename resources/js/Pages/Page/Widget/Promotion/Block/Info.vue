<template>
    <el-row :gutter="10" v-if="!editWidget">
        <el-col :span="6">
            <el-tooltip content="Изображение" placement="top-start" effect="dark">
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
            <el-tooltip content="Иконка" placement="top-start" effect="dark">
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
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Виджет">
                    {{ promotion.name }}
                </el-descriptions-item>
                <el-descriptions-item label="Шаблон">
                    {{ promotion.template }}
                </el-descriptions-item>
                <el-descriptions-item label="Ссылка">
                    {{ promotion.url }}
                </el-descriptions-item>
                <el-descriptions-item label="Активен">
                    <Active :active="promotion.active"/>
                </el-descriptions-item>
                <el-descriptions-item label="Заголовок">
                    {{ promotion.caption }}
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ promotion.description }}
                </el-descriptions-item>
                <el-descriptions-item label="Баннер">
                    {{ (promotion.banner) ? promotion.banner.name : '' }}
                </el-descriptions-item>
                <el-descriptions-item label="Акция">
                    {{ (promotion.promotion) ? promotion.promotion.name : '' }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-button v-if="!editWidget" type="warning" @click="editWidget = true">Изменить</el-button>
    <el-row :gutter="10" v-if="editWidget">
        <el-col :span="8">
            <el-form label-width="auto" style="width: 500px;">
                <el-form-item label="Виджет">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Шаблон">
                    <el-select v-model="form.template">
                        <el-option v-for="item in templates" :key="item.value" :value="item.value" :label="item.label"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Банер">
                    <el-select v-model="form.banner_id" clearable>
                        <el-option v-for="item in banners" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Акция">
                    <el-select v-model="form.promotion_id" clearable>
                        <el-option v-for="item in promotions" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="form.url"/>
                </el-form-item>
                <el-form-item label="Заголовок">
                    <el-input v-model="form.caption"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="form.description" type="textarea" rows="3"/>
                </el-form-item>
                <el-button type="info" @click="editWidget = false">Отмена</el-button>
                <el-button type="success" @click="setWidget">Сохранить</el-button>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение"
                v-model:image="promotion.image"
                @selectImageFile="onSelectImage"
            />
            <UploadImageFile
                label="Иконка"
                v-model:image="promotion.icon"
                @selectImageFile="onSelectIcon"
            />
        </el-col>
    </el-row>
</template>

<script setup lang="ts">
import {defineProps, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import UploadImageFile from "@Comp/UploadImageFile.vue";

const props = defineProps({
    promotion: Object,
    templates: Array,
    banners: Array,
    promotions: Array,
})
const editWidget = ref(false)
const form = reactive({
    name: props.promotion.name,
    template: props.promotion.template,
    banner_id: props.promotion.banner_id,
    promotion_id: props.promotion.promotion_id,
    url: props.promotion.url,
    caption: props.promotion.caption,
    description: props.promotion.description,
    image: null,
    clear_image: false,
    icon: null,
    clear_icon: false,
})


function setWidget() {
    router.visit(route('admin.page.widget.promotion.set-widget', {promotion: props.promotion.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            editWidget.value = false;
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
