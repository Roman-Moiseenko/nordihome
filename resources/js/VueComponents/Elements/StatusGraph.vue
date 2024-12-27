<template>
    <el-tooltip effect="dark" placement="top-start" :content="show.content" :visible="show.visible">
        <el-progress v-if="show.value < 0" type="circle" :percentage="100" :width="width" :stroke-width="stroke"
                     :show-text="false" :color="'#dddddd'"/>
        <el-progress v-if="show.value == 0" type="circle" :percentage="100" :width="width" status="exception"
                     :stroke-width="stroke" :show-text="false"/>
        <el-progress v-if="show.value == 0.5" type="circle" :percentage="100" :width="width" status="warning"
                     :stroke-width="stroke" :show-text="false"/>
        <el-progress v-if="show.value == 1" type="circle" :percentage="100" :width="width" status="success"
                     :stroke-width="stroke" :show-text="false"/>
        <template v-if="show.value > 1.01" class="flex">
            <el-progress type="circle" :percentage="100" :width="width" status="success" :stroke-width="stroke"
                         :show-text="false"/>
            <el-progress type="circle" :percentage="100" :width="width" status="success" style="margin-left: -6px"
                         :stroke-width="stroke" :show-text="false"/>
        </template>
    </el-tooltip>
</template>

<script lang="ts" setup>
import {computed, defineProps, ref} from 'vue'

const props = defineProps({
    value: {
        type: Number,
        default: 0.0,
    },
    type: {
        type: String,
        default: null,
    },
})
const width = ref(16)
const stroke = ref(8)


const show = computed(() => {

    if (props.value < 0)
        return {
            value: -1,
            content: '',
            visible: false,
        }
    if (props.value === 0)
        return {
            value: 0,
            content: props.type === 'pay' ? 'Просрочка оплаты' : 'Не выдан',
            visible: null,
        }
    if (props.value === 1)
        return {
            value: 1,
            content: props.type === 'pay' ? 'Оплачен' : 'Выдан',
            visible: null,
        }
    if (props.value > 1)
        return {
        value: 1,
        content: props.type === 'pay' ? 'Переплата' : '',
        visible: null,
    }

    return {
        value: 0.5,
        content: props.type === 'pay' ? 'Оплачен частично' : 'Выдан частично',
        visible: null,
    }
})
</script>
