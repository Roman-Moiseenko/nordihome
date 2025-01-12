<template>
    <el-config-provider :locale="ru">
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl">
            Распоряжение на выдачу {{ expense.number }} [{{ expense.status_text }}] - {{ expense.type_text }}
        </h1>
        <div class="mt-3 p-3 bg-white rounded-lg ">
            <ExpenseInfo :expense="expense"/>
        </div>
        <el-affix target=".affix-container" :offset="64">
            <div class="bg-white rounded-lg my-2 p-1 shadow flex">
                <ExpenseActions :expense="expense"/>
            </div>
        </el-affix>
        <div class="mt-1 px-3 py-1 bg-white rounded-md">
            <el-table
                :data="[...expense.items]"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column type="index" label="п/п"/>
                <el-table-column prop="product.code" label="Артикул" width="110"/>
                <el-table-column prop="product.name" label="Товар / Вес, Объем" width="240" show-overflow-tooltip/>

                <el-table-column prop="quantity" label="Кол-во" width="110" align="center"/>
                <el-table-column prop="comment" align="right" label="Комментарий"/>
            </el-table>
            <el-table
                :data="[...expense.additions]"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
                class="mt-2"
            >
                <el-table-column type="index" label="п/п"/>
                <el-table-column prop="addition.name" label="Товар / Вес, Объем" width="240" show-overflow-tooltip/>

                <el-table-column prop="amount" label="Кол-во" width="110" align="center"/>
                <el-table-column prop="comment" align="right" label="Комментарий"/>
            </el-table>

        </div>

    </el-config-provider>

</template>

<script setup lang="ts">
import {Head, Link} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import {defineProps} from "vue";
import ExpenseInfo from "./Blocks/Info.vue"
import ExpenseActions from "./Blocks/Actions.vue"


const props = defineProps({
    expense: Object,
    title: {
        type: String,
        default: 'Распоряжение на выдачу',
    },

})
</script>

<style scoped>

</style>
