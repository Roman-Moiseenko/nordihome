<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-descriptions v-if="!editUser" :column="1" border class="mb-5">
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
            <div class="mt-auto">
                <el-button v-if="!user.active" type="primary" @click="onActive">Активировать</el-button>
            </div>
        </el-col>
        <el-col :span="8">
            <h2>Покупки</h2>
            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item>
                    <template #label>
                        <i class="fa-sharp fa-light fa-bags-shopping"></i>
                    </template>
                    {{ user.quantity }} заказа(ов)
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <i class="fa-light fa-ruble-sign"></i>
                    </template>
                    {{ func.price(user.amount) }}
                </el-descriptions-item>
                <el-descriptions-item>
                    <template #label>
                        <i class="fa-light fa-scanner-gun"></i>
                    </template>
                    {{ user.pricing }}
                </el-descriptions-item>
            </el-descriptions>

            <el-button type="success" @click="createOrder">Сделать заказ</el-button>
        </el-col>
        <el-col :span="8">
            <h2>Организации</h2>
            <div v-for="item in user.organizations">
                <template v-if="item.pivot.default">
                    <el-tag  type="success">{{ item.short_name }}</el-tag>
                </template>
                <template v-else>
                    {{ item.short_name }}
                    <el-tooltip effect="dark" content="Назначить по-умолчанию" placement="top-start">
                        <el-button type="success" size="small" @click="defaultOrganization(item.id)" style="margin-left: 4px">
                            <i class="fa-light fa-check"></i>
                        </el-button>
                    </el-tooltip>
                    <el-button type="danger" size="small" @click="detachOrganization(item.id)" style="margin-left: 4px">
                        <i class="fa-light fa-trash"></i>
                    </el-button>
                </template>
                <Link type="primary" class="ml-3" :href="route('admin.accounting.organization.show', {organization: item.id})">
                    <i class="fa-light fa-right"></i>
                </Link>
            </div>
            <div class="mt-3">
                <div v-show="!showEdit">
                    <el-button type="warning" size="small" @click="showEdit = true">Добавить</el-button>
                </div>
                <div v-show="showEdit" class="flex items-center">
                    <el-select v-model="organization" style="width: 260px;">
                        <el-option v-for="item in organizations" :key="item.id" :value="item.id" :label="item.short_name">
                        </el-option>
                    </el-select>
                    <el-button type="success" size="small" @click="attachOrganization" class="ml-3">
                        <i class="fa-light fa-floppy-disk"></i>
                    </el-button>
                    <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                        <i class="fa-light fa-xmark"></i>
                    </el-button>
                </div>
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {ref, reactive} from "vue";
import {router, Link} from "@inertiajs/vue3";

const props = defineProps({
    user: Object,
    organizations: Array,
    deliveries: Array,
    type_pricing: Array,
})

const showEdit = ref(false)
const editUser = ref(false)
const organization = ref(null)
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

function onActive() {
    router.visit(route('admin.user.verify', {user: props.user.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}
function createOrder() {
    router.post(route('admin.order.store', {user_id: props.user.id}))
}
function attachOrganization() {
    showEdit.value = false;
    router.post(route('admin.user.attach', {user: props.user.id}), {organization: organization.value})
}
function detachOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.user.detach', {user: props.user.id, organization: id}))
}
function defaultOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.user.default', {user: props.user.id, organization: id}))
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
function deliveryText() {
    for (let key in props.deliveries) {
        let item = props.deliveries[key]
        if (item.value === props.user.delivery) return item.label
    }
}
</script>
