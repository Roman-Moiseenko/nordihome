<template>
    <div v-show="!showEdit" class="flex">
        <template v-if="isFIO">
            {{ func.fullName(field) }}
        </template>
        <template v-else-if="!isdate">
            {{ field }}
        </template>
        <template v-else>
            {{ func.date(field)}}
        </template>

        <el-button class="ml-auto" type="warning" plain size="small" @click.stop="showEdit = true">
            <i class="fa-light fa-pen-to-square"></i>
        </el-button>
    </div>
    <div v-show="showEdit" class="flex">
            <el-input v-if="isFIO && !isdate" v-model="field_fio.surname"  @click.stop=""/>
            <el-input v-if="isFIO && !isdate" v-model="field_fio.firstname"  @click.stop=""/>
            <el-input v-if="isFIO && !isdate" v-model="field_fio.secondname"  class="mr-2" @click.stop=""/>

            <el-input v-if="!isFIO && !isdate" v-model="field_new" class="mr-2" :formatter="formatter" @click.stop=""/>   <!--  style="width: 220px;"-->

        <el-date-picker v-if="isdate" v-model="field_new" type="date" />

        <el-button type="success" size="small" @click.stop="saveField" class="my-auto">
            <i class="fa-light fa-floppy-disk"></i>
        </el-button>
        <el-button type="info" size="small" @click.stop="showEdit = false" style="margin-left: 4px" class="my-auto">
            <i class="fa-light fa-xmark"></i>
        </el-button>
    </div>
</template>
<script lang="ts" setup>
import {defineEmits, defineProps, reactive, ref} from "vue";
import {func} from '@Res/func.js'

const props = defineProps({
    field: Object,
    formatter: Function,
    isdate: {
        default: false,
        type: Boolean,
    },
    isFIO: {
        default: false,
        type: Boolean,
    },
})

const showEdit = ref(false)
const field_new = ref(props.field)
console.log(field_new.value)
const field_fio = reactive({
    surname: null,
    firstname: null,
    secondname: null,
})
if (props.isFIO) {
    field_fio.surname = props.field.surname
    field_fio.firstname = props.field.firstname
    field_fio.secondname = props.field.secondname
}
const $emit = defineEmits(['update:field', 'change'])
function saveField(){
    showEdit.value = false
    if (props.isFIO) {
        $emit('update:field', field_fio)
    } else {
        $emit('update:field', field_new.value)
    }
    $emit('change', true)
}
</script>
<style scoped>

</style>
