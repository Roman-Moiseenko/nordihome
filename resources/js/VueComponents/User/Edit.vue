<template>
    <el-descriptions v-if="!editUser" :column="1" border class="mb-5" :size="small ? 'small' : 'default'">
        <el-descriptions-item label="ФИО">
            {{ client.lastName }} {{ client.firstName }} {{ client.middleName }}
        </el-descriptions-item>
        <el-descriptions-item label="Телефон">
            {{ client.phone }}
        </el-descriptions-item>
        <el-descriptions-item label="Email">
            {{ client.email }}
        </el-descriptions-item>
        <el-descriptions-item label="Доставка">

        </el-descriptions-item>
        <el-descriptions-item label="Адрес">
            {{ client.postalCode}} {{ client.region }} {{ client.street }}
        </el-descriptions-item>
    </el-descriptions>
    <el-button v-if="!editUser" type="warning" @click="editUser = true">Изменить</el-button>
    <el-form v-if="editUser" label-width="auto">
        <el-form-item label="ФИО">
            <div class="flex">
                <el-input v-model="form.lastName" placeholder="Фамилия" />
                <el-input v-model="form.firstName" placeholder="Имя" />
                <el-input v-model="form.middleName" placeholder="Отчество" />
            </div>
        </el-form-item>
        <el-form-item label="Телефон">
            <el-input v-model="form.phone" placeholder="8 (000)-000-00-00" :formatter="val => func.MaskPhone(val)"/>
        </el-form-item>
        <el-form-item label="Email">
            <el-input v-model="form.email" placeholder="email@email.ru" />
        </el-form-item>
        <el-form-item label="Индекс, Регион">
            <div class="flex">
                <el-input v-model="form.postalCode" placeholder="Индекс" :formatter="val => func.MaskInteger(val, 6)"/>
                <el-input v-model="form.region" placeholder="Регион" />
            </div>
        </el-form-item>
        <el-form-item label="Улица">
            <el-input v-model="form.street" placeholder="Город, Улица, Д., Кв." />
        </el-form-item>

        <el-form-item label="Уровень цен">
            <el-select v-model="form.priceType">
                <el-option v-for="item in type_pricing" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
        </el-form-item>
        <el-form-item label="Персональная скидка*">
            <el-input v-model="form.discount" placeholder="Город, Улица, Д., Кв." />
        </el-form-item>
        <el-button type="info" @click="editUser = false">Отмена</el-button>
        <el-button type="success" @click="setInfo">Сохранить</el-button>
    </el-form>
</template>


<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";
import {func} from '@Res/func.js'
import {router} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    client: Object,
    small: {
        type: Boolean,
        default: false,
    }
})
console.log(props.client)

const deliveries = ref([])
const type_pricing = ref([])

/*
onMounted(() => {
    axios.post(route('admin.client.user-params')).then(result => {
        deliveries.value = [...result.data.deliveries]
        type_pricing.value = [...result.data.type_pricing]

    })
})
*/


const editUser = ref(false)
const form = reactive({
    phone: props.client.phone,
    email: props.client.email,
    firstName: props.client.firstName,
    middleName: props.client.middleName,
    lastName: props.client.lastName,

    country: props.client.country,
    region: props.client.region,
    regionCode: props.client.regionCode,
    city: props.client.city,
    street: props.client.street,
    postalCode: props.client.postalCode,
    priceType: props.client.priceType,
    discount: props.client.discount,

})

function deliveryText() {
    for (let key in deliveries.value) {
        let item = deliveries.value[key]
        if (item.value === props.client.delivery) {
            return item.label
        }
    }
}
function setInfo() {
    router.visit(route('admin.client.set-info', {client: props.client.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            editUser.value = false;
        }
    })
}
</script>
