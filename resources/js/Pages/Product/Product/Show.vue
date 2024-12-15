<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">{{ product.name }}</h1>
        В разработке, <br>
        Таблица, по убыванию даты:<br>
        | Поступление (с ссылкой на док) | Себестоимость РУБ | Кол-во | Сумма | Продано шт. | Цена продажи | Сумма | Чист.прибыль РУБ | % прибыли | ГТД |

        <br>
        <br>
        Таблица продаж.<br>
        | Дата | Ссылка на заказ клиента | Клиент (ФИО, ООО, Гость) | шт | сумма |


        <br>
        <br>
        Идея?<br>
        График продаж, помесячно, в штуках. Выбираем год <br> возможно выбирать масштаб "за месяц", "за год", "за все время" (по годно)
    </el-config-provider>
</template>

<script setup lang="ts">
import {Head} from "@inertiajs/vue3";
import {defineProps} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'

const props = defineProps({
    product: Object,
    title: {
        type: String,
        default: 'Статистка по товару',
    },
})
</script>
