<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Бренд">
                    {{ brand.name }}
                    <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                        <i class="fa-light fa-pen-to-square"></i>
                    </el-button>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Описание">
                    {{ brand.description }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Ссылка на сайт">
                    {{ brand.url }}
                </el-descriptions-item>
                <el-descriptions-item label="Класс Парсера">
                    {{ brand.parser_class }}
                </el-descriptions-item>
                <el-descriptions-item v-if="brand.currency" label="Валюта парсера">
                    {{ brand.currency.name }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>

    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Название бренда">
                    <el-input v-model="info.name" />
                </el-form-item>


                <el-form-item label="Ссылка на сайт">
                    <el-input v-model="info.url"/>
                </el-form-item>
                <el-form-item label="Класс Парсера">
                    <el-select v-model="info.parser_class">
                        <el-option v-for="item in parsers" :key="item.value" :value="item.value" :label="item.label" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Валюта">
                    <el-select v-model="info.currency_id">
                        <el-option v-for="item in currencies" :key="item.id" :value="item.id" :label="item.name" />
                    </el-select>
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
                v-model:image="brand.image"
                @selectImageFile="onSelectImage"
            />
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название Бренда</b> является обязательным полем.</p>
                <p>Поле <b>Ссылка на сайт</b> используется в Shcema для SEO</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку 700х700.</p>
                <p>Если используется <b>Парсер</b>, то Ссылка на тот сайт, который парсится</p>
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
    brand: Object,
    parsers: Array,
    currencies: Array,
})
const iSavingInfo = ref(false)
const info = reactive({
    name: props.brand.name,
    description: props.brand.description,
    url: props.brand.url,
    parser_class: props.brand.parser_class,
    sameAs: props.brand.sameAs,
    currency_id: props.brand.currency_id,
    file: null,
    clear_file: false,
})
const showEdit = ref(false)

function onSetInfo() {
    router.visit(
        route('admin.product.brand.set-info', {brand: props.brand.id}), {
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
