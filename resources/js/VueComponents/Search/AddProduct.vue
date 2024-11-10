<template>
    <div class="flex">
        <el-select
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


        </el-select>
        <el-input-number id="quantity" v-if="props.quantity" v-model="form.quantity" @keyup.enter="onQuantity" class="ml-1"/>
        <el-checkbox id="preorder" v-if="props.preorder" v-model="form.product_id" label="Под заказ" @change="onPreorder" class="ml-1"/>
        <el-button id="button" type="primary" @click="onAdd" class="ml-1">{{ props.caption }}</el-button>
    </div>
</template>

<script lang="ts" setup>
import {inject, reactive, ref, defineProps, computed} from "vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";

const search = route('admin.product.search-add')
const props = defineProps({
    route: String, //Ссылка на добавление товара в документ. Метод POST.
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
        default: 'Добавить товар',
        type: String
    },
    column: {  //Вертикальное расположение элементов
        default: false,
        type: Boolean
    },
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
        axios.post(search, {search: query}).then(response => {
           // console.log(response.data);
            options.value = response.data
            loading.value = false
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
    } else {
        document.getElementById('button').focus()
    }
}
function onQuantity() {
    document.getElementById('button').focus()
}
function onAdd() {
    if (form.product_id === null) return;

    router.visit(props.route, {
        method: "post",
        data: form,
        onSuccess: page => {
            form.product_id = null
            form.quantity = 1
            form.preorder = null
        }
    })
}
</script>
<style scoped>

</style>
