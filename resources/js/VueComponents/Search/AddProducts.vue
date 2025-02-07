<template>
    <el-tooltip content="Загрузить товары из файла" effect="dark" placement="top-start">
        <el-button type="primary" plain @click="openDialog" class="ml-1"><i class="fa-light fa-upload"></i></el-button>
    </el-tooltip>

    <el-dialog v-model="uploadDialog" title="Пакетная загрузка товаров" width="400">
        <HelpBlock>
            <div>Для распарсивания отсутствующих товаров по списку, выберите бренд</div>
            <div>Формат файла <b>xlsx</b></div>
            <div>Первая колонка - артикулы, Вторая - кол-во, Третья (опционно) - Цена</div>
        </HelpBlock>
        <el-form label-width="auto">
            <el-select v-model="formCreate.brand_id" filterable @change="selectBrand" clearable class="my-3"
                       placeholder="Бренд">
                <el-option v-for="item in brands" :value="item.id" :label="item.name"/>
            </el-select>
            <el-upload
                class="upload-demo"
                :action="route_upload"
                :on-success="handleSuccess"
                :on-error="handleError"

            >
                <el-button type="primary">Выбрать файл</el-button>
            </el-upload>
        </el-form>
        <el-tag v-show="!disabledUnload" type="success" class="my-2">{{ textUpload }}</el-tag>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="uploadDialog = false">Отмена</el-button>
                <el-button type="primary" @click="onUpload" :disabled="disabledUnload">Загрузить</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import {reactive, ref, defineProps, inject} from "vue";
import {router} from "@inertiajs/vue3";
import {ElLoading, ElMessage, type UploadProps} from "element-plus";
import axios from "axios";
import HelpBlock from "@Comp/HelpBlock.vue";

const props = defineProps({
    route: String,
    caption: {
        default: 'Добавить товары',
        type: String
    }
})
const uploadDialog = ref(false);
interface ISelect {
    id: Number,
    name: String,
}

interface IProduct {
    product_id: Number,
    quantity: Number,
    price: Number,
}

const products = ref([])
const brands = ref<ISelect[]>([]);
const formCreate = reactive({
    brand_id: null,
})
const route_upload = ref(null)
const disabledUnload = ref(true)
const upload = ref<UploadInstance>()
const textUpload = ref(null)

function selectBrand() {
    route_upload.value = route('admin.product.upload', {brand_id: formCreate.brand_id});
}
function openDialog() {
    //Загружаем список брендов и категорий в диалог
    if (brands.value.length === 0) {
        const loading = ElLoading.service({
            lock: false,
            text: 'Загружаем бренды',
            background: 'rgba(0, 0, 0, 0.7)',
        })
        route_upload.value = route('admin.product.upload');
        axios.post(route('admin.product.brand.list')).then(response => {
            brands.value = [...response.data]
            loading.close()
            uploadDialog.value = true
        });
    } else {
        uploadDialog.value = true
    }

}
const handleSuccess: UploadProps['onSuccess'] = (response, uploadFile, uploadFiles) => {
    textUpload.value = 'Товары считаны - ' + response.length.toString() + ' шт.'
    disabledUnload.value = false;
    products.value = [...response]
}
const handleError: UploadProps['onError'] = (error, uploadFile, uploadFiles) => {
    console.log(error, uploadFile)
}

function onUpload() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Добавляем товары',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    router.visit(props.route, {
        method: "post",
        data: {products: products.value,},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            disabledUnload.value = false
            uploadDialog.value = false
            loading.close()
        }
    })

    /*
    ElMessage({
        message: 'В разработке',
        type: 'warning',
        plain: true,
        showClose: true,
        duration: 5000,
        center: true,
    }); */
}

</script>
<style lang="scss">
.in-focus {
    position: absolute;
    z-index: 999;
    top: 0;
    width: 180px;
}

.out-focus {
    > textarea {
        overflow: hidden !important;
    }
}
</style>
