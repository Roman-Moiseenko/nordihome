<template>
    <el-dialog v-model="dialogCreate" title="Новый клиент" width="500">
        <el-form>
            <el-form-item>
                <el-input v-model="formCreate.phone" placeholder="Телефон" :formatter="val => func.MaskPhone(val)"/>
            </el-form-item>
            <el-form-item>
                <el-input v-model="formCreate.email" placeholder="Email"/>
            </el-form-item>
            <el-form-item>
                <div class="flex">
                    <el-input v-model="formCreate.fullname.surname" placeholder="Фамилия"/>
                    <el-input v-model="formCreate.fullname.firstname" placeholder="Имя"/>
                    <el-input v-model="formCreate.fullname.secondname" placeholder="Отчество"/>
                </div>
            </el-form-item>
            <el-form-item>
                <div class="flex">
                    <el-input v-model="formCreate.inn" placeholder="ИНН"
                              :formatter="val => func.MaskInteger(val, 12)"/>
                    <el-input v-model="formCreate.bik" placeholder="БИК"
                              :formatter="val => func.MaskInteger(val, 9)"/>
                </div>
            </el-form-item>
            <el-form-item>
                <el-input v-model="formCreate.account" placeholder="Р/счет"
                          :formatter="val => func.MaskPhone(val, 20)"/>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="onCancel">Отмена</el-button>
                <el-button type="primary" @click="saveUser">Сохранить</el-button>
            </div>
        </template>

    </el-dialog>
</template>

<script setup lang="ts">
import {func} from '@Res/func.js'
import {reactive, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
})
const dialogCreate = ref(props.show)
watch(() => props.show, (newValues, oldValues) => {
    dialogCreate.value = props.show;
});
const $emit = defineEmits(['update:user'])
const formCreate = reactive({
    email: null,
    phone: null,
    fullname: {
        surname: null,
        firstname: null,
        secondname: null,
    },
    inn: null,
    bik: null,
    account: null,
})

function saveUser() {
    axios.post(route('admin.user.create'), formCreate).then(response => {

        if (response.data.error !== undefined) console.log(response.data.error)
        $emit('update:user', response.data)
    }).catch(reason => {
        console.log('reason', reason)
    });
}
function onCancel() {
    $emit('update:user', null)
}
</script>

<style scoped>

</style>
