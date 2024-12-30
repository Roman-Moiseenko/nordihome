<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <!--EditUser  :user="user" :deliveries="deliveries" :type_pricing="type_pricing"/-->

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
                    {{ user.delivery_name }}
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

            <div v-if="!user.active" class="mt-3">
                <el-button type="primary" @click="onActive">Активировать</el-button>
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
                <SearchAttachOrganization
                    :route="route('admin.user.attach', {user: props.user.id})"/>
            </div>
            <h2>Файлы физ.лица</h2>
            <el-upload
                v-model:file-list="fileList"
                action="#"
                :on-preview="handlePreview"
                :on-remove="handleRemove"
                :auto-upload="false"
                @input="upload($event.target.files[0])"
                :on-error="handleError"
            >
                <template #trigger>
                    <el-button type="info" circle>
                        <el-icon>
                            <Paperclip/>
                        </el-icon>
                    </el-button>
                </template>
                <template #file="scope">

                </template>
            </el-upload>
        </el-col>
    </el-row>
</template>

<script lang="ts" setup>
import {func} from '@Res/func.js'
import {ref, reactive, handleError} from "vue";
import {router, Link} from "@inertiajs/vue3";
import SearchAttachOrganization from "@Comp/Search/AttachOrganization.vue";
import {UploadUserFile} from "element-plus";
import axios from "axios";
import EditUser from "@Comp/User/Edit.vue";

const props = defineProps({
    user: Object,
    deliveries: Array,
    type_pricing: Array,
})

const showEdit = ref(false)
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
        preserveState: true,
        onSuccess: page => {
            editUser.value = false;
        }
    })
}
/*
function deliveryText() {
    for (let key in props.deliveries) {
        let item = props.deliveries[key]
        if (item.value === props.user.delivery) return item.label
    }
} */

///Файлы ===>
const fileList = ref<UploadUserFile[]>([]);

for (let key in props.user.files) {
    fileList.value.push({
        id: props.user.files[key].id,
        name: props.user.files[key].title,
    });
}
function upload(file) {
    router.visit(route('admin.user.upload', {user: props.user.id}), {
        method: "post",
        data: {
            file: file,
        },
        preserveScroll: true,
        preserveState: true,
    })
}

const handleRemove: UploadProps['onRemove'] = (file, uploadFiles) => {
    router.post(route('admin.file.remove-file'), {id: file.id})
}
const handlePreview: UploadProps['onPreview'] = (uploadFile) => {
    axios.post(route('admin.file.download'),null,
        {
            responseType: 'arraybuffer',
            params: {id: uploadFile.id},
        }
    ).then(res=>{
        let blob = new Blob([res.data], {type: 'application/*'})
        let link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = uploadFile.name
        link._target = 'blank'
        document.body.appendChild(link);
        link.click();
        URL.revokeObjectURL(link.href)
    })
}

//<=== Файлы
</script>
