<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        {{ currency.name }}
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <el-row :gutter="10">
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Название">
                        <el-input v-model="form.name" :disabled="iSaving" :readonly="!isEdit" />
                    </el-form-item>
                    <el-form-item label="Обозначение">
                        <el-input v-model="form.sign" :disabled="iSaving" :readonly="!isEdit" />
                    </el-form-item>
                    <el-form-item label="Курс">
                        <el-input v-model="form.exchange" :disabled="iSaving" :readonly="!isEdit" />
                    </el-form-item>
                    <el-form-item label="Обозначение по ЦБ">
                        <el-input v-model="form.cbr_code" :disabled="iSaving" :readonly="!isEdit" />
                    </el-form-item>
                    <el-form-item label="Наценка в %">
                        <el-input v-model="form.extra" :disabled="iSaving" :readonly="!isEdit" />
                    </el-form-item>
                    <el-form-item label="По умолчанию">
                        <el-checkbox v-model="form.default" :disabled="iSaving || !isEdit" :checked="currency.default"/>
                    </el-form-item>
                    <el-button type="primary" v-if="!isEdit" @click="isEdit = true">Редактировать</el-button>
                    <el-button type="info" v-if="isEdit" @click="isEdit = false">Отменить</el-button>
                    <el-button type="success" v-if="isEdit" @click="setItem">Сохранить</el-button>
                </el-form>
            </el-col>
            <el-col :span="8">
                <HelpBlock>
                    <p>Для валют, у которых заполнено поле <strong>Обозначение по ЦБ</strong> ежедневно будет проходить синхронизация с курсом ЦБ России.</p>
                    <p>Если нужно зафиксировать курс, удалите обозначение по ЦБ</p>
                    <p>Для изменения курса ЦБ используйте наценку (%) к текущему курсу</p>
                </HelpBlock>
            </el-col>
        </el-row>
    </div>



</template>

<script lang="ts" setup>
import {ref, defineProps, reactive} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import EditField from "@Comp/Elements/EditField.vue";
import HelpBlock from "@Comp/HelpBlock.vue";


const props = defineProps({
    currency: Object,
    title: {
        type: String,
        default: 'Карточка валюты',
    },

})


const form = reactive({
    name: props.currency.name,
    sign: props.currency.sign,
    exchange: props.currency.exchange,
    cbr_code: props.currency.cbr_code,
    extra: props.currency.extra,
    default: props.currency.default,
})
const iSaving = ref(false)
const isEdit = ref(false)
function setName(name) {

}

function setItem() {
    iSaving.value = true;
    router.visit(route('admin.accounting.currency.update', {currency: props.currency.id}), {
        method: "put",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

</script>
