<template>
    <el-row :gutter="10" v-if="!showEdit">
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Хранилище">
                    {{ storage.name }}
                    <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                        <i class="fa-light fa-pen-to-square"></i>
                    </el-button>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Адрес">
                    {{ storage.post }} {{ storage.city }} {{ storage.address }}
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <el-descriptions :column="3" border class="mb-5">
                <el-descriptions-item label="Продажа">
                    <Active :active="storage.point_of_sale" />
                </el-descriptions-item>
                <el-descriptions-item label="Выдача">
                    <Active :active="storage.point_of_delivery" />
                </el-descriptions-item>
                <el-descriptions-item label="По-умолчанию">
                    <Active :active="storage.default" />
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
    </el-row>
    <el-row :gutter="10" v-if="showEdit">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Название">
                    <el-input v-model="form.name"/>
                </el-form-item>
                <el-form-item label="Ссылка/Slug">
                    <el-input v-model="form.slug"/>
                </el-form-item>
                <el-form-item label="Почтовый индекс">
                    <el-input v-model="form.post"/>
                </el-form-item>
                <el-form-item label="Город">
                    <el-input v-model="form.city"/>
                </el-form-item>
                <el-form-item label="Адрес">
                    <el-input v-model="form.address"/>
                </el-form-item>
                <el-form-item label="Широта">
                    <el-input v-model="form.latitude"/>
                </el-form-item>
                <el-form-item label="Долгота">
                    <el-input v-model="form.longitude"/>
                </el-form-item>
                <el-form-item label="Точка продаж">
                    <el-checkbox v-model="form.point_of_sale" :checked="form.point_of_sale"/>
                </el-form-item>
                <el-form-item label="Точка выдачи">
                    <el-checkbox v-model="form.point_of_delivery" :checked="form.point_of_delivery"/>
                </el-form-item>
                <el-form-item label="Основной склад">
                    <el-checkbox v-model="form.default" :checked="form.default"/>
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
                v-model:image="storage.image"
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
                    <div><b>Название Хранилища</b> является обязательным полем.</div>
                    <div class="mt-2">Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</div>
                    <div class="mt-2">Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</div>
                    <div class="mt-2"><b>latitude</b> и <b>longitude</b> используются для виджета карты.</div>
                    <div class="mt-2">Поле <b>Адрес</b> используется также для отображения на карте виджета.</div>
                </div>
            </div>

        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {ref, reactive, defineProps} from "vue";
import {router, Link} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import UploadImageFile from '@Comp/UploadImageFile.vue'

const props = defineProps({
    storage: Object,
})
const form = reactive({
    name: props.storage.name,
    slug: props.storage.slug,
    post: props.storage.post,
    city: props.storage.city,
    address: props.storage.address,
    latitude: props.storage.latitude,
    longitude: props.storage.longitude,
    point_of_sale: props.storage.point_of_sale,
    point_of_delivery: props.storage.point_of_delivery,
    default: props.storage.default,
    file: props.storage.image,
    clear_file: false,
});

const showEdit = ref(false)

function onSetInfo() {

    router.visit(
        route('admin.accounting.storage.set-info', {storage: props.storage.id}), {
            method: "post",
            data: form,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
        );
}

function loadImage() {
    //router.post(route('admin.accounting.distributor.attach', {distributor: props.distributor.id}), {organization: organization.value})
}
function onSelectImage(val) {
    form.clear_file = val.clear_file;
    form.file = val.file
}
/*
const info = reactive({
    document: {
        number: props.arrival.number,
        created_at: props.arrival.created_at,
        incoming_number: props.arrival.incoming_number,
        incoming_at: props.arrival.incoming_at,
        comment: props.arrival.comment,
    },
    storage_id: props.arrival.storage_id,
    exchange_fix: props.arrival.exchange_fix,
    operation: props.arrival.operation,
    gtd: props.arrival.gtd,
})
const notEdit = computed(() => props.arrival.completed);
*/
function setInfo() {
   /* iSavingInfo.value = true
    router.visit(route('admin.accounting.arrival.set-info', {arrival: props.arrival.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })*/
}
</script>
