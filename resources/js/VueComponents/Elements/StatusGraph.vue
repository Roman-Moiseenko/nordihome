<template>
        <el-progress v-if="show == 0" type="circle" :percentage="100" :width="width" status="exception" :stroke-width="stroke" :show-text="false"/>
        <el-progress v-if="show == 0.5" type="circle" :percentage="100" :width="width" status="warning" :stroke-width="stroke" :show-text="false"/>
        <el-progress v-if="show == 1" type="circle" :percentage="100" :width="width" status="success" :stroke-width="stroke" :show-text="false"/>
        <template v-if="show > 1.01" class="flex">
            <el-progress  type="circle" :percentage="100" :width="width" status="success" :stroke-width="stroke" :show-text="false"/>
            <el-progress  type="circle" :percentage="100" :width="width" status="success" style="margin-left: -6px" :stroke-width="stroke" :show-text="false"/>
        </template>

</template>

<script lang="ts" setup>
import {computed, defineProps, ref} from 'vue'
const props = defineProps({
    value: {
        type: Number,
        default: 0.0,
    }
})
const width = ref(16)
const stroke = ref(8)

const show = computed(() => {
    if (props.value === 0) return 0
    if (props.value === 1) return 1
    if (props.value > 1) return 2
    return 0.5
})
</script>
