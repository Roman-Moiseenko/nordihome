<template>
    <el-row :gutter="10" v-if="!editInfo">
        <el-col :span="24">
            <el-descriptions v-if="!editInfo" :column="2" border class="mb-5">
                <el-descriptions-item label="Название">
                    {{ feed.set_title }}
                </el-descriptions-item>
                <el-descriptions-item label="Пред.цена">
                    <Active :active="feed.set_preprice" />
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    {{ feed.set_description }}
                </el-descriptions-item>

            </el-descriptions>
        </el-col>

    </el-row>
    <el-button v-if="!editInfo" type="warning" @click="editInfo = true">Изменить</el-button>
    <el-row v-if="editInfo" :gutter="10">
        <el-col :span="16">
            <el-form label-width="auto">

                <el-form-item label="Название">
                    <el-input v-model="info.set_title"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.set_description" clearable type="textarea" rows="3"/>
                </el-form-item>
                <el-form-item label="Показывать пред.цену">
                    <el-checkbox v-model="info.set_preprice" :checked="feed.set_preprice" />
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
            <HelpBlock>
                <p><b>Название</b> магазина для Яндекс и Гугл маркета.</p>
                <p>Если <b>Название и Описание</b> - не заполнены, то берутся из настроек Продавца.</p>
                <p>Остальные <b>Поля</b> будут появляться по мере необходимости.</p>

            </HelpBlock>
        </el-col>
    </el-row>
</template>


<script setup lang="ts">
import Active from "@Comp/Elements/Active.vue";
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import HelpBlock from "@Comp/HelpBlock.vue";
import UploadImageFile from "@Comp/UploadImageFile.vue";

const props = defineProps({
    feed: Object,
})
const editInfo = ref(false)
const info = reactive({
    set_preprice: props.feed.set_preprice,
    set_title: props.feed.set_title,
    set_description: props.feed.set_description,
})

function onSetInfo() {
    router.visit(
        route('admin.unload.feed.set-info', {feed: props.feed.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                editInfo.value = false;
            }
        }
    );
}
</script>
