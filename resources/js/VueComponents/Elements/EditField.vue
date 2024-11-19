<template>
    <div v-show="!showEdit">
        <template v-if="isFIO">
            {{ func.fullName(field) }}
        </template>
        <template v-else>
            {{ field }}
        </template>

        <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
            <i class="fa-light fa-pen-to-square"></i>
        </el-button>
    </div>
    <div v-show="showEdit">
        <template v-if="isFIO">
            <el-input v-model="field_fio.surname" style="width: 120px;" />
            <el-input v-model="field_fio.firstname" style="width: 100px;"/>
            <el-input v-model="field_fio.secondname" style="width: 120px;" class="mr-2"/>
        </template>
        <template v-else>
            <el-input v-model="field_new" style="width: 220px;" class="mr-2" :formatter="formatter"/>
        </template>

        <el-button type="success" size="small" @click="saveField">
            <i class="fa-light fa-floppy-disk"></i>
        </el-button>
        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
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
    isFIO: {
        default: false,
        type: Boolean,
    },
})
console.log(props)
const showEdit = ref(false)
const field_new = ref(props.field)
const field_fio = reactive({
    surname: props.field.surname,
    firstname: props.field.firstname,
    secondname: props.field.secondname,
})
const $emit = defineEmits(['update:field'])
function saveField(){
    showEdit.value = false
    if (props.isFIO) {
        $emit('update:field', field_fio)
    } else {
        $emit('update:field', field_new.value)
    }


}
</script>
<style scoped>

</style>
