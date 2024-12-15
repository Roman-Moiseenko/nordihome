<template>
    <div v-show="!showEdit" class="flex">
        <template v-if="isFIO">
            {{ func.fullName(field) }}
        </template>
        <template v-else>
            {{ field }}
        </template>

        <el-button class="ml-auto" type="warning" plain size="small" @click.stop="showEdit = true">
            <i class="fa-light fa-pen-to-square"></i>
        </el-button>
    </div>
    <div v-show="showEdit" class="flex">
            <el-input v-if="isFIO" v-model="field_fio.surname"  @click.stop=""/>
            <el-input v-if="isFIO" v-model="field_fio.firstname"  @click.stop=""/>
            <el-input v-if="isFIO" v-model="field_fio.secondname"  class="mr-2" @click.stop=""/>

            <el-input v-if="!isFIO" v-model="field_new" class="mr-2" :formatter="formatter" @click.stop=""/>   <!--  style="width: 220px;"-->

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
    isFIO: {
        default: false,
        type: Boolean,
    },
})

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
