<template>
    <el-dialog v-model="dialogCreate" title="Новый клиент" width="500">
        <el-form>
            <el-form-item>
                <el-input v-model="formCreate.phone" placeholder="Телефон" :formatter="val => func.MaskPhone(val)"
                          @keydown.enter="handlerEnter(focusEmail)" ref="focusPhone" autofocus />
            </el-form-item>
            <el-form-item>
                <el-input v-model="formCreate.email" placeholder="Email"
                          @keydown.enter="handlerEnter(focusSurname)" ref="focusEmail"  />
            </el-form-item>
            <el-form-item>
                <div class="flex">
                    <el-input v-model="formCreate.fullname.surname" placeholder="Фамилия"
                              @keydown.enter="handlerEnter(focusFirstname)" ref="focusSurname"/>
                    <el-input v-model="formCreate.fullname.firstname" placeholder="Имя"
                              @keydown.enter="handlerEnter(focusSecondname)" ref="focusFirstname"/>
                    <el-input v-model="formCreate.fullname.secondname" placeholder="Отчество"
                              @keydown.enter="handlerEnter(focusINN)" ref="focusSecondname"/>
                </div>
            </el-form-item>
            <el-form-item>
                <div class="flex">
                    <el-input v-model="formCreate.inn" placeholder="ИНН"
                              :formatter="val => func.MaskInteger(val, 12)"
                              @keydown.enter="handlerEnter(focusBIK)" ref="focusINN"/>
                    <el-input v-model="formCreate.bik" placeholder="БИК"
                              :formatter="val => func.MaskInteger(val, 9)"
                              @keydown.enter="handlerEnter(focusAccount)" ref="focusBIK"/>
                </div>
            </el-form-item>
            <el-form-item>
                <el-input v-model="formCreate.account" placeholder="Р/счет"
                          :formatter="val => func.MaskPhone(val, 20)"
                          @keydown.enter="handlerEnter(null)" ref="focusAccount"/>
            </el-form-item>
        </el-form>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="onCancel" ref="focusCancel">Отмена</el-button>
                <el-button type="primary" @click="saveUser" ref="focusSave" id="SaveButton">Сохранить</el-button>
            </div>
        </template>

    </el-dialog>
</template>

<script setup lang="ts">
import {func} from '@Res/func.js'
import {reactive, ref, watch, onMounted} from "vue";
import {router} from "@inertiajs/vue3";
import axios from "axios";

const focusPhone = ref()
const focusEmail = ref()
const focusSurname = ref()
const focusFirstname = ref()
const focusSecondname = ref()
const focusINN = ref()
const focusBIK = ref()
const focusAccount = ref()

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
})

function handlerEnter(focusElement) {
    if (focusElement === null) {
        document.getElementById('SaveButton').focus()
    } else {
        focusElement?.focus()
    }
}

const dialogCreate = ref(props.show)
watch(() => props.show, (newValues, oldValues) => {
    dialogCreate.value = props.show

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
