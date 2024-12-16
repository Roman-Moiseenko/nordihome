<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
    <h1 class="font-medium text-xl">Правило {{ discount.name }}</h1>
    <div class="p-5 bg-white rounded-md">
        <el-row :gutter="10" v-if="!showEdit">
            <el-col :span="8">
                <el-descriptions :column="1" border class="mb-5">
                    <el-descriptions-item label="Внутреннее имя">
                        {{ discount.name }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Заголовок для клиента">
                        {{ discount.title }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Скидка">
                        {{ discount.discount }}%
                    </el-descriptions-item>
                </el-descriptions>
            </el-col>
            <el-col :span="8">
                <el-descriptions :column="1" border class="mb-5">
                    <el-descriptions-item label="Тип">
                        {{ discount.type }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Условие">
                        {{ discount.caption }}
                    </el-descriptions-item>

                </el-descriptions>
            </el-col>
        </el-row>
        <el-button v-if="!showEdit" type="warning" @click="showEdit = true">
            <i class="fa-light fa-pen-to-square"></i>&nbsp;Редактировать
        </el-button>
        <el-row :gutter="10" v-if="showEdit">
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item label="Внутреннее имя">
                        <el-input v-model="info.name"/>
                    </el-form-item>
                    <el-form-item label="Заголовок для клиента">
                        <el-input v-model="info.title"/>
                    </el-form-item>
                    <el-form-item label="Базовая скидка">
                        <el-input v-model="info.discount" :formatter="val => func.MaskInteger(val)">
                            <template #append>%</template>
                        </el-input>
                    </el-form-item>

                    <el-form-item label="С ">
                        <el-input v-if="discount.class == 'CostEnabledDiscount'" v-model="info._from">
                            <template #append>₽</template>
                        </el-input>
                        <el-select v-if="discount.class == 'PeriodWeekEnabledDiscount'" v-model="info._from">
                            <el-option v-for="item in weeks" :key="item.value" :value="item.value" :label="item.label"/>
                        </el-select>
                        <el-input-number v-if="discount.class == 'PeriodMonthEnabledDiscount'" v-model="info._from" min="1" max="31"/>
                        <el-date-picker v-if="discount.class == 'FullTimeEnabledDiscount'"  v-model="info._from" type="date" />
                        <el-date-picker v-if="discount.class == 'PeriodYearEnabledDiscount'"  v-model="info._from" type="date"
                        format="DD MMMM"/>

                    </el-form-item>
                    <el-form-item label="По">
                        <el-input v-if="discount.class == 'CostEnabledDiscount'" v-model="info._to" >
                            <template #append>₽</template>
                        </el-input>
                        <el-select v-if="discount.class == 'PeriodWeekEnabledDiscount'" v-model="info._to">
                            <el-option v-for="item in weeks" :key="item.value" :value="item.value" :label="item.label"/>
                        </el-select>
                        <el-input-number v-if="discount.class == 'PeriodMonthEnabledDiscount'" v-model="info._to" min="1" max="31"/>
                        <el-date-picker v-if="discount.class == 'FullTimeEnabledDiscount'"  v-model="info._to" type="date" />
                        <el-date-picker v-if="discount.class == 'PeriodYearEnabledDiscount'"  v-model="info._to" type="date"
                                        format="DD MMMM"/>
                    </el-form-item>


                    <el-button type="info" @click="showEdit = false" style="margin-left: 4px">
                        Отмена
                    </el-button>
                    <el-button type="success" @click="onSetInfo">
                        Сохранить
                    </el-button>
                </el-form>
            </el-col>
            <el-col :span="8">

            </el-col>
            <el-col :span="8">
                <HelpBlock>

                </HelpBlock>
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


const props = defineProps({
    discount: Object,
    title: {
        type: String,
        default: 'Карточка скидки',
    },
})

console.log(props.discount)
const showEdit = ref(false)
const info = reactive({
    name: props.discount.name,
    title: props.discount.title,
    discount: props.discount.discount,
    _from: props.discount._from,
    _to: props.discount._to,

})

const weeks = ref([
    {value: '1', label: 'Понедельник'},
    {value: '2', label: 'Вторник'},
    {value: '3', label: 'Среда'},
    {value: '4', label: 'Четверг'},
    {value: '5', label: 'Пятница'},
    {value: '6', label: 'Суббота'},
    {value: '0', label: 'Воскресенье'},
])

function onSetInfo() {
    if (props.discount.class === 'FullTimeEnabledDiscount') {
        info._from = func.date(info._from)
        info._to = func.date(info._to)
    }
    if (props.discount.class === 'PeriodYearEnabledDiscount') {
        info._from = func.date(info._from, false)
        info._to = func.date(info._to, false)
    }


    router.visit(
        route('admin.discount.discount.set-info', {discount: props.discount.id}), {
            method: "post",
            data: info,
            onSuccess: page => {
                showEdit.value = false;
            }
        }
    );
}

</script>
<style scoped>

</style>
