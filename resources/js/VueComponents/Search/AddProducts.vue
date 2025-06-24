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
import {reactive, ref, defineProps, inject, watch} from "vue";
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
    let _products = [...response.products]
    if (response.error !== undefined || response.error !== null) {
        textUpload.value = 'Найдено ' + _products.length.toString() + ' позиций';
        disabledUnload.value = false;
        findProducts(_products)
    } else {
        textUpload.value = 'Ошибка загрузки. ' + response.error;
        disabledUnload.value = true;
    }
}

function findProducts(data) {
    const loading = ElLoading.service({
        lock: false,
        text: 'Ищем/парсим товары 0 из ' + data.length.toString(),
        background: 'rgba(0, 0, 0, 0.7)',
    })
    const count = ref(0);
    let count_error = 0;
    watch(() => count.value, (newValues, oldValues) => {
        if (newValues === data.length) loading.close();
    });
    let i = 0;
    data.forEach(function (product) {
        i++;
        setTimeout(() => {
            axios.post(route('admin.product.find-parser'), {code: product.code, brand_id: formCreate.brand_id}).then(response => {
                console.log('response', response.data)
                count.value++;
                if ((response.data.error === undefined || response.data.error === null) && response.data !== 0) {
                    products.value.push({
                        product_id: response.data,
                        quantity: product.quantity,
                        price: product.price,
                        price2: product.price2,
                    })
                } else  {
                    console.log('response', response.data.error)
                    count_error++;
                }
                loading.text.value = 'Ищем/парсим товары ' + count.value + ' из ' + data.length.toString() + (count_error !== 0 ? ('. Ошибок=' + count_error) : '');
            }).catch(resolve => {
                console.log('resolve', resolve)
            })

        }, i * 100);



    });


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
        preserveState: false,
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
