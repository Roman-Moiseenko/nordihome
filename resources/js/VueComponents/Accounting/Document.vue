<template>
    <el-form label-width="auto">
        <el-form-item v-if="distributor" label="Поставщик">
            <el-input :model-value="distributor.name + ' (' + distributor.short_name + ')'" :readonly="true"
                      style="width: 400px"/>
            <el-tag type="primary" class="ml-3">Долг</el-tag>
            <span :class="'ml-2 font-medium ' + ((distributor.debit - distributor.credit) > 0 ? 'text-red-600' : 'text-green-600')  ">
                {{ func.price(distributor.debit - distributor.credit, distributor.currency) }}
            </span>
        </el-form-item>
        <el-form-item label="№ документа">
            <el-input v-model="modelValue.number" @change="setInfo" :disabled="saving" :readonly="edit"
                      style="width: 160px"/>
            <span class="text-gray-500 px-4">от</span>
            <el-date-picker v-model="modelValue.created_at" type="datetime"
                            @change="setInfo" :disabled="saving"
                            style="width: 200px"
                            :readonly="edit"
            />
        </el-form-item>
        <el-form-item label="№ вход.док.">
            <el-input v-model="modelValue.incoming_number" @change="setInfo" :disabled="saving" :readonly="edit"
                      style="width: 160px"/>
            <span class="text-gray-500 px-4">от</span>
            <el-date-picker v-model="modelValue.incoming_at" type="date"
                            @change="setInfo" :disabled="saving"
                            style="width: 160px"
                            :readonly="edit"
            />
        </el-form-item>
        <el-form-item label="Комментарий">
            <el-input v-model="modelValue.comment" @change="setInfo" :disabled="saving" :readonly="edit" type="textarea"
                      :rows="2"/>
        </el-form-item>
    </el-form>
</template>

<script setup>
import {func} from '@Res/func.js'
import {defineEmits, defineProps} from "vue";

const props = defineProps({
    distributor: {
        type: Object,
        default: null,
    },
    modelValue: {
        number: String,
        created_at: Date,
        incoming_number: String,
        incoming_at: Date,
        comment: String,
    },
    saving: Boolean,
    edit: Boolean,
})
const $emit = defineEmits(['update:modelValue'])


function setInfo() {
    // console.log(props.modelValue)
    props.modelValue.created_at = func.datetime(props.modelValue.created_at)
    props.modelValue.incoming_at = func.date(props.modelValue.incoming_at)
    $emit('update:modelValue', props.modelValue)
}
</script>
