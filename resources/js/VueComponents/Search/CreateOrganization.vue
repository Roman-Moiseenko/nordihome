<template>
    <el-dialog v-model="_show" title="Добавить контрагента" width="500">
        <el-form label-width="auto">
            <el-form-item label="ИНН">
                <el-input v-model="formCreate.inn" />
            </el-form-item>
            <el-form-item v-if="formCreate.foreign" label="Название">
                <el-input v-model="formCreate.name" />
            </el-form-item>
            <el-form-item label="БИК">
                <el-input v-model="formCreate.bik" />
            </el-form-item>
            <el-form-item v-if="formCreate.foreign"  label="Банк">
                <el-input v-model="formCreate.bank" />
            </el-form-item>


            <el-form-item label="Р/счет">
                <el-input v-model="formCreate.account" />
            </el-form-item>
            <el-form-item label="*">
                <el-checkbox v-model="formCreate.foreign" label="Иностранная компания"/>
            </el-form-item>
            <el-button type="info" class="" @click="onCancel">
                Отмена
            </el-button>
            <el-button type="primary" class="" @click="onCreate">
                Создать
            </el-button>
        </el-form>
    </el-dialog>
</template>

<script lang="ts" setup>
import axios from "axios";
import {ElMessage} from "element-plus";
import {defineEmits, reactive, ref, watch} from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    }
})
//dialogCreate = false
const $emit = defineEmits(['create:id', 'create:cancel'])
const formCreate =reactive({
    inn: null,
    name: null,
    bik: null,
    bank: null,
    account: null,
    foreign: false,
})
const _show = ref(false)

watch(() => props.show, (newValues, oldValues) => {
    _show.value = newValues;
});

function onCancel() {
    $emit('create:cancel', false)
}

function onCreate() {
    axios.post(route('admin.accounting.organization.find'), formCreate).then(response => {
        if (response.data.error === undefined) {
            $emit('create:id', response.data)
            $emit('create:cancel', false)
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
