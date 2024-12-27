<template>
    <el-select v-model="addition" filterable style="width: 200px;" class="ml-2" placeholder="Добавить услугу">
        <el-option-group v-for="group in additions"
                         :key="group.label"
                         :label="group.label">
            <el-option v-for="item in group.additions"
                       :key="item.id"
                       :label="item.name"
                       :value="item.id"/>
        </el-option-group>
    </el-select>
    <el-button type="primary" plain class="ml-1" @click="handleSelect">Добавить</el-button>
</template>

<script setup lang="ts">
import {defineProps, defineEmits, ref} from 'vue'
import {router} from "@inertiajs/vue3";

const props = defineProps({
    additions: Array,
    order: Object,
})

const addition = ref(null)
function handleSelect() {
    router.post(route('admin.order.add-addition', {order: props.order.id}), {addition_id: addition.value})
    addition.value = null
}
</script>

<style scoped>

</style>
