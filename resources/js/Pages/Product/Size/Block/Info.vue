<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Группа">
                    {{ group.name }}
                    <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                        <i class="fa-light fa-pen-to-square"></i>
                    </el-button>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Описание">
                    {{ group.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="3" border class="mb-5">
                <el-descriptions-item label="Своя страница">
                    <Active :active="group.published" />
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>

    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Название группы">
                    <el-input v-model="info.name" />
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug"/>
                </el-form-item>
                <el-form-item label="Страница на сайте">
                    <el-checkbox v-model="info.published"  :checked="info.published"/>
                </el-form-item>
                <el-form-item label="Описание">
                    <el-input v-model="info.description" type="textarea" :rows="3" />
                </el-form-item>

                <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button type="success" size="small" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="8">
            <UploadImageFile
                label="Изображение для сайта"
                v-model:image="group.image"
                @selectImageFile="onSelectImage"
            />

        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название Группы</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на группу) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит. Ссылка используется, если у группы есть своя траница на стороне клиента</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку 700х700.</p>
            </HelpBlock>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import Active from "@Comp/Elements/Active.vue";
import HelpBlock from "@Comp/HelpBlock.vue";

const props = defineProps({
    group: Object,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.group.name,
    description: props.group.description,
    slug: props.group.slug,
    published: props.group.published,
    file: null,
    clear_file: false,
})
const showEdit = ref(false)

function onSetInfo() {
    router.visit(
        route('admin.product.group.set-info', {group: props.group.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}
function onSelectImage(val) {
    info.clear_file = val.clear_file;
    info.file = val.file
}
</script>
