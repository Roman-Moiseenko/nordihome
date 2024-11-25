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
                <el-button size="small" @click="showCreate = true">Создать?</el-button>
            </template>
        </el-select>
        <el-button id="button" type="success" size="small" @click="attachOrganization" class="ml-3">
            <i class="fa-light fa-floppy-disk"></i>
        </el-button>
        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
            <i class="fa-light fa-xmark"></i>
        </el-button>
    </div>

    <CreateOrganization :show="showCreate" @create:id="onCreate" @create:cancel="showCreate = value" />
</template>

<script lang="ts" setup>
import {ref, onMounted, reactive} from "vue";
import axios from "axios";
import {router} from "@inertiajs/vue3";
import {ElMessage} from "element-plus";
import CreateOrganization from "@Comp/Search/CreateOrganization.vue";

const props = defineProps({
    route: String,
})
const loading = ref(false)
//const dialogCreate = ref(false)
const showCreate = ref(false)
interface IFirm {
    id: Number,
    short_name: String,
    inn: String,

}
const search = route('admin.accounting.organization.search-add')
const organization = ref(null)
const organizations = ref<IFirm[]>([])
/*const formCreate =reactive({
    inn: null,
    bik: null,
    account: null,
})
*/

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

function onCreate(val) {
    showCreate.value = false
    organization.value = val
    attachOrganization()
}
/*
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
*/
</script>

<style scoped>

</style>
