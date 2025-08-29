<template>
    <el-descriptions v-if="!editUser" :column="1" border class="mb-5" :size="small ? 'small' : 'default'">
        <el-descriptions-item label="ФИО">
            {{ func.fullName(user.fullname) }}
        </el-descriptions-item>
        <el-descriptions-item label="Телефон">
            {{ func.phone(user.phone) }}
        </el-descriptions-item>
        <el-descriptions-item label="Email">
            {{ user.email }}
        </el-descriptions-item>
        <el-descriptions-item label="Доставка">
            {{ deliveryText() }}
        </el-descriptions-item>
        <el-descriptions-item label="Адрес">
            {{ user.address.post }} {{ user.address.region }} {{ user.address.address }}
        </el-descriptions-item>
    </el-descriptions>
    <el-button v-if="!editUser" type="warning" @click="editUser = true">Изменить</el-button>
    <el-form v-if="editUser" label-width="auto">
        <el-form-item label="ФИО">
            <div class="flex">
                <el-input v-model="form.fullname.surname" placeholder="Фамилия" />
                <el-input v-model="form.fullname.firstname" placeholder="Имя" />
                <el-input v-model="form.fullname.secondname" placeholder="Отчество" />
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
                <el-input v-model="form.address.post" placeholder="Индекс" :formatter="val => func.MaskInteger(val, 6)"/>
                <el-input v-model="form.address.region" placeholder="Регион" />
            </div>
        </el-form-item>
        <el-form-item label="Адрес">
            <el-input v-model="form.address.address" placeholder="Город, Улица, Д., Кв." />
        </el-form-item>
        <el-form-item label="Доставка">
            <el-select v-model="form.delivery">
                <el-option v-for="item in deliveries" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
        </el-form-item>
        <el-form-item label="Цена товара">
            <el-select v-model="form.client">
                <el-option v-for="item in type_pricing" :key="item.value" :value="item.value" :label="item.label" />
            </el-select>
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
    user: Object,
    small: {
        type: Boolean,
        default: false,
    }
})
const deliveries = ref([])
const type_pricing = ref([])
onMounted(() => {
    axios.post(route('admin.user.user-params')).then(result => {
        deliveries.value = [...result.data.deliveries]
        type_pricing.value = [...result.data.type_pricing]

    })
})

const editUser = ref(false)
const form = reactive({
    phone: props.user.phone,
    email: props.user.email,
    fullname: {
        surname: props.user.fullname.surname,
        firstname: props.user.fullname.firstname,
        secondname: props.user.fullname.secondname,
    },
    address: {
        post: props.user.address.post,
        region: props.user.address.region,
        address: props.user.address.address,
    },
    delivery: props.user.delivery,
    client: props.user.client,
})

function deliveryText() {
    for (let key in deliveries.value) {
        let item = deliveries.value[key]
        if (item.value === props.user.delivery) {
            return item.label
        }
    }
}
function setInfo() {
    router.visit(route('admin.user.set-info', {user: props.user.id}), {
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
