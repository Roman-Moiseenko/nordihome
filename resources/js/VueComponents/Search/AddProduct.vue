<template>
    <div class="flex">
        <el-select
            id="select"
            v-model="form.product_id"
            filterable
            remote
            reserve-keyword
            placeholder="Введите артикул или название"
            :remote-method="remoteMethod"
            :loading="loading"
            :style="width"
            @keyup.enter="onSelect"
        >
            <el-option
                v-for="item in options"
                :key="item.id"
                :value="item.id"
                :label="item.name + ' ('+ item.code + ')'"
            >
                <el-tag v-if="props.showStock" :type="item.stock ? 'success' : 'danger'" round effect="dark">*</el-tag>
                {{ item.name }} ({{ item.code }})
            </el-option>
            <template #loading>
                Загрузка
            </template>
            <template #empty>
                Не найдено <el-button v-if="create" size="small" @click="createProduct">Создать?</el-button>
            </template>
        </el-select>
        <el-input-number id="quantity" v-if="props.quantity" v-model="form.quantity" @keyup.enter="onQuantity" class="ml-1" min="1" style="width: 120px;"/>
        <el-checkbox id="preorder" v-if="props.preorder" v-model="form.preorder" label="Под заказ" @change="onPreorder" class="ml-1"/>
        <el-button id="button-search-product" :type="button" @click="onAdd" class="ml-1">
            <i class="fa-light fa-box mr-2"></i>
            {{ props.caption }}
        </el-button>
    </div>
    <el-dialog v-model="dialogCreate" title="Новый товар" width="400">
        <el-form label-width="auto">
            <el-form-item label="Товар">
                <el-input v-model="formCreate.name" />
            </el-form-item>
            <el-form-item label="Артикул">
                <el-input v-model="formCreate.code" />
            </el-form-item>
            <el-form-item label="Бренд">
                <el-select v-model="formCreate.brand_id" filterable @change="selectBrand">
                    <el-option v-for="item in brands" :value="item.id" :label="item.name"/>
                </el-select>
            </el-form-item>
            <el-form-item v-if="showParser" label="Спарсить товар">
                <el-checkbox v-model="formCreate.parser" :checked="formCreate.parser" />
            </el-form-item>
            <el-form-item label="Категория">
                <el-select v-model="formCreate.category_id" filterable>
                    <el-option v-for="item in categories" :value="item.id" :label="item.name"/>
                </el-select>
            </el-form-item>

            <el-button type="info" class="" @click="dialogCreate = false">
                Отмена
            </el-button>
            <el-button type="primary" class="" @click="storeProduct">
                Создать
            </el-button>
        </el-form>
    </el-dialog>

</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps, computed} from "vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";
import {ElLoading} from "element-plus";

const search = route('admin.product.search-add')
const props = defineProps({
    route: String, //Ссылка на добавление товара в документ. Метод POST.
    search: {
        type: String,
        default: route('admin.product.search-add'),
    },
    quantity: {  //Поле quantity
        default: false,
        type: Boolean
    },
    published: {  //Только опубликованные товары (для Заказа)
        default: false,
        type: Boolean
    },
    width: {//Ширина в пикселях поискового поля
        default: 240,
        type: Number
    },
    showImage: { //Показывать изображение в списке !! На будущее !!
        default: false,
        type: Boolean
    },
    showStock: {//Показывать кружок в наличии или нет товар
        default: false,
        type: Boolean
    },
    showCount:{//Показывать кол-во в списке
        default: false,
        type: Boolean
    },
    preorder: {//Поле "Под заказ"
        default: false,
        type: Boolean
    },
    caption: {  //Подпись для кнопки
        default: 'Добавить',
        type: String
    },
    column: {  //Вертикальное расположение элементов
        default: false,
        type: Boolean
    },
    create: {  //Кнопка Создать? Когда не найден
        default: false,
        type: Boolean
    },
    preserveState: {
        default: false,
        type: Boolean
    },
    params: { //Доп.параметры для post-запроса
        default: {},
        type: Object,
    },
    button: {
        default: 'primary',
        type: String,
    }
})
const width = computed<String>( () => 'width: ' + props.width + 'px;')
interface ListItem {
    id: Number
    name: String,
    code: String,
    stock: Boolean,
}
const options = ref<ListItem[]>([])
const loading = ref(false)
const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(props.search, {search: query}).then(response => {
            if (response.data.error !== undefined) console.log(response.data.error)
            form.product_id = response.data[0].id;
            options.value = response.data
            loading.value = false
        }).catch(reason => {
            console.log('reason', reason)
        });
    } else {
        options.value = []
    }
}
const form = reactive({
    product_id: null,
    quantity: 1,
    preorder: null,
})

function onSelect() {
    if (props.quantity) {
        document.getElementById('quantity').focus()
        document.getElementById('quantity').select()
    } else {
        document.getElementById('button-search-product').focus()
    }
}
function onQuantity() {
    document.getElementById('button-search-product').focus()
}
function onAdd() {
    if (form.product_id === null) return;

    router.visit(props.route, {
        method: "post",
        data: {...form, ...props.params},
        preserveScroll: true,
        preserveState: props.preserveState,
        onSuccess: page => {
            form.product_id = null
            form.quantity = 1
            form.preorder = null
            document.getElementById('select').focus()
        }
    })
}

//Доб.товара ===>
const showParser = ref(false)
const dialogCreate = ref(false)
const formCreate = reactive({
    name: null,
    code: null,
    brand_id: null,
    category_id: null,
    parser: false,
})
interface ISelect {
    id: Number,
    name: String,
}
const brands = ref<ISelect[]>([]);
const categories = ref<ISelect[]>([]);

function createProduct() {
    //Загружаем список брендов и категорий в диалог
    if (brands.value.length === 0) {
        const loading = ElLoading.service({
            lock: false,
            text: 'Загружаем категории',
            background: 'rgba(0, 0, 0, 0.7)',
        })

        axios.post(route('admin.product.brand.list')).then(response => {
            brands.value = [...response.data]
            axios.post(route('admin.product.category.list')).then(response => {
                categories.value = [...response.data]
                dialogCreate.value = true
                loading.close()
            });

        });
    } else {
        dialogCreate.value = true
    }
}
function storeProduct() {
    const loading = ElLoading.service({
        lock: false,
        text: 'Идет создание товара',
        background: 'rgba(0, 0, 0, 0.7)',
    })
    axios.post(route('admin.product.fast-create'), formCreate).then(response => {
        if (response.data.product_id === undefined) {
            form.product_id = null
            console.log(JSON.parse(response.data))
        } else {
            form.product_id = response.data.product_id
            onAdd()
        }

        dialogCreate.value = false
        loading.close()
    });
}
function selectBrand(){
    showParser.value = false
    brands.value.forEach(function (item) {
        if (formCreate.brand_id === item.id && item.parser)
            showParser.value = true
    })
}
//<============
</script>
<style scoped>

</style>
