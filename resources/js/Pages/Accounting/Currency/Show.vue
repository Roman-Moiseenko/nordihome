<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">
        {{ currency.name }}
    </h1>
    <div class="mt-3 p-3 bg-white rounded-lg ">
        <el-row>
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
                <div class="mt-10 bg-warning/20 border border-warning rounded-md relative p-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="lightbulb" data-lucide="lightbulb" class="lucide lucide-lightbulb w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3"><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 018.91 14"></path></svg>
                    <h2 class="text-lg font-medium">
                        Информация
                    </h2>
                    <div class="mt-5 font-medium"></div>
                    <div class="leading-relaxed mt-2 text-slate-600">
                        <div>Для валют, у которых заполнено поле <strong>Обозначение по ЦБ</strong> ежедневно будет проходить синхронизация с курсом ЦБ России.</div>
                        <div>Если нужно зафиксировать курс, удалите обозначение по ЦБ</div>
                        <div>Для изменения курса ЦБ используйте наценку (%) к текущему курсу</div>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>



</template>

<script lang="ts" setup>
import {ref, defineProps, reactive} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import EditField from "@Comp/Elements/EditField.vue";


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
        preserveState: false,
        onSuccess: page => {
            iSaving.value = false;
        }
    })
}

</script>
