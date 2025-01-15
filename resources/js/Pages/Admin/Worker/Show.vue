<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Рабочий {{ func.fullName(worker.fullname) }}</h1>
        <div class="p-5 bg-white rounded-md">
            <el-row :gutter="10" v-if="!showEdit">
                <el-col :span="8">
                    <el-descriptions :column="1" border class="mb-5">
                        <el-descriptions-item label="ФИО">
                            {{ func.fullName(worker.fullname) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Телефон">
                            {{ func.phone(worker.phone) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Специализация">
                            <el-tag v-if="worker.driver" type="info" class="ml-1">Водитель</el-tag>
                            <el-tag v-if="worker.loader" type="info" class="ml-1">Грузчик</el-tag>
                            <el-tag v-if="worker.assemble" type="info" class="ml-1">Сборщик</el-tag>
                            <el-tag v-if="worker.logistic" type="info" class="ml-1">Логист</el-tag>
                        </el-descriptions-item>
                        <el-descriptions-item label="Телеграм">
                            {{ worker.telegram_user_id }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Склад">
                            {{ worker.storage_name }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Активен">
                            <Active :active="worker.active"/>
                        </el-descriptions-item>
                    </el-descriptions>
                </el-col>
                <el-col :span="8">
                    <el-descriptions :column="1" border class="mb-5">



                    </el-descriptions>
                </el-col>
            </el-row>
            <el-button v-if="!showEdit" type="warning" @click="showEdit = true">
                <i class="fa-light fa-pen-to-square"></i>&nbsp;Редактировать
            </el-button>
            <el-row :gutter="10" v-if="showEdit">
                <el-col :span="8">
                    <el-form label-width="auto">
                        <el-form-item label="ФИО" label-position="top" class="mt-3">
                            <div class="flex">
                                <el-input v-model="form.surname" placeholder="Фамилия"/>
                                <el-input v-model="form.firstname" placeholder="Имя"/>
                                <el-input v-model="form.secondname" placeholder="Отчество"/>
                            </div>
                        </el-form-item>
                        <el-form-item label="Телефон" class="mt-3">
                            <el-input v-model="form.phone" :formatter="val => func.MaskPhone(val)"/>
                        </el-form-item>
                        <el-form-item label="Телеграм" class="mt-3">
                            <el-input v-model="form.telegram_user_id" :formatter="val => func.MaskInteger(val)"/>
                        </el-form-item>
                        <el-form-item label="Специализация">
                            <el-checkbox v-model="form.driver" :checked="form.driver" label="Водитель"/>
                            <el-checkbox v-model="form.loader" :checked="form.loader"  label="Грузчик"/>
                            <el-checkbox v-model="form.assemble" :checked="form.assemble"  label="Сборщик"/>
                            <el-checkbox v-model="form.logistic" :checked="form.logistic"  label="Логист"/>
                        </el-form-item>
                        <el-form-item label="Склад">
                            <el-select v-model="form.storage_id" clearable>
                                <el-option v-for="item in storages" :value="item.id" :label="item.name"/>
                            </el-select>
                        </el-form-item>
                        <el-button type="info" @click="showEdit = false" style="margin-left: 4px">
                            Отмена
                        </el-button>
                        <el-button type="success" @click="onSetInfo">
                            Сохранить
                        </el-button>
                    </el-form>
                </el-col>

            </el-row>
        </div>

    </el-config-provider>
</template>
<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import {reactive, ref} from "vue";
import HelpBlock from "@Comp/HelpBlock.vue";
import {func} from "@Res/func"
import Active from "@Comp/Elements/Active.vue";


const props = defineProps({
    worker: Object,
    title: {
        type: String,
        default: 'Карточка Рабочего',
    },
    storages: Array,
})

console.log(props.worker)
const showEdit = ref(false)
const form = reactive({
    surname: props.worker.fullname.surname,
    firstname: props.worker.fullname.firstname,
    secondname: props.worker.fullname.secondname,
    phone: props.worker.phone,
    telegram_user_id: props.worker.telegram_user_id,
    driver: props.worker.driver === 1 ? true : false,
    loader: props.worker.loader === 1 ? true : false,
    assemble: props.worker.assemble === 1 ? true : false,
    logistic: props.worker.logistic === 1 ? true : false,
    storage_id: props.worker.storage_id,
})


function onSetInfo() {

    router.visit(
        route('admin.worker.update', {worker: props.worker.id}), {
            method: "post",
            data: form,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

</script>
<style scoped>

</style>
