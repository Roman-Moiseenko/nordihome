<template>
    <div v-show="!showEdit">
        <el-button type="warning" size="small" @click="showEdit = true">Добавить</el-button>
    </div>
    <div v-show="showEdit" class="flex items-center">
        <el-select
            v-model="organization"
            style="width: 260px;"
            filterable
            remote
            reserve-keyword
            placeholder="Введите ИНН или название"
            :remote-method="remoteMethod"
            :loading="loading"
            @keyup.enter="onSelect"
        >
            <el-option v-for="item in organizations" :key="item.id" :value="item.id" :label="item.short_name">
                {{ item.short_name }} ({{ item.inn }})
            </el-option>

            <template #loading>
                Загрузка
            </template>
            <template #empty>
                Не найдено
                <el-button size="small" @click="dialogCreate = true">Создать?</el-button>
            </template>
        </el-select>
        <el-button id="button" type="success" size="small" @click="attachOrganization" class="ml-3">
            <i class="fa-light fa-floppy-disk"></i>
        </el-button>
        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
            <i class="fa-light fa-xmark"></i>
        </el-button>
    </div>

    <el-dialog v-model="dialogCreate" title="Добавить контрагента" width="400">
        <el-form label-width="auto">
            <el-form-item label="ИНН">
                <el-input v-model="formCreate.inn" />
            </el-form-item>
            <el-form-item label="БИК">
                <el-input v-model="formCreate.bik" />
            </el-form-item>

            <el-form-item label="Р/счет">
                <el-input v-model="formCreate.account" />
            </el-form-item>
            <el-button type="info" class="" @click="dialogCreate = false">
                Отмена
            </el-button>
            <el-button type="primary" class="" @click="addOrganization">
                Создать
            </el-button>
        </el-form>
    </el-dialog>

</template>

<script lang="ts" setup>
import {ref, onMounted, reactive} from "vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import {ElMessage} from "element-plus";

const props = defineProps({
    route: String,
})
const loading = ref(false)
const dialogCreate = ref(false)
interface IFirm {
    id: Number,
    short_name: String,
    inn: String,

}
const search = route('admin.accounting.organization.search-add')
const organization = ref(null)
const organizations = ref<IFirm[]>([])
const formCreate =reactive({
    inn: null,
    bik: null,
    account: null,
})


const showEdit = ref(false)

const remoteMethod = (query: string) => {
    if (query) {
        loading.value = true
        axios.post(search, {search: query}).then(response => {
            organizations.value = response.data
            loading.value = false
        });
    } else {
        organizations.value = []
    }
}
function attachOrganization() {
    router.visit(props.route, {
        method: "post",
        data: {organization: organization.value},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            showEdit.value = false;
        }
    })
}
function onSelect() {
    document.getElementById('button').focus()
}
function addOrganization() {
    axios.post(route('admin.accounting.organization.find'), formCreate).then(response => {
        if (response.data.error === undefined) {
            organization.value = response.data
            dialogCreate.value = false
            attachOrganization()
        } else {
            //Сообщение
            ElMessage({
                message: response.data.error,
                type: 'error',
                plain: true,
                showClose: true,
                duration: 5000,
                center: true,
            });
        }


    });
}
</script>

<style scoped>

</style>
