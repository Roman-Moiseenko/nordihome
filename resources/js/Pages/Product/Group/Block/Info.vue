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
            <div class="bg-warning/20 border border-warning rounded-md relative p-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="lightbulb" data-lucide="lightbulb" class="lucide lucide-lightbulb w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3"><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 018.91 14"></path></svg>
                <h2 class="text-lg font-medium">
                    Информация
                </h2>
                <div class="mt-5 font-medium"></div>
                <div class="leading-relaxed mt-2 text-slate-600">
                    <div><b>Название Группы</b> является обязательным полем.</div>
                    <div class="mt-2">Поле <b>Slug</b> (ссылка на группу) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит. Ссылка используется, если у группы есть своя траница на стороне клиента</div>
                    <div class="mt-2">Рекомендуемое разрешение для <b>картинок</b> в карточку 700х700.</div>
                </div>
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import UploadImageFile from '@Comp/UploadImageFile.vue'
import Active from "@Comp/Elements/Active.vue";

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
