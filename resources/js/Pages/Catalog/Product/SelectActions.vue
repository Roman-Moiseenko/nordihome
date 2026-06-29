<template>
    <div :class="'flex ' + (props.type === 'top' ? 'mb-2' : '') + (props.type === 'bottom' ? 'mt-2' : '')">
        <el-select v-model="action" placeholder="Действие" style="width: 200px;">
            <el-option value="draft" label="В черновик"/>
            <el-option value="published" label="Опубликовать"/>
            <el-option value="not_sale" label="Снять с продажи"/>
            <el-option value="to_sale" label="В продажу"/>
            <el-option value="remove" label="Удалить"/>
        </el-select>
        <el-button type="primary" plain class="ml-2" @click="onAction">Применить</el-button>
    </div>
</template>
<script setup lang="ts">
import {ref} from "vue";

const props = defineProps({
    type: {
        type: String,
        validator: value => value === 'top' || value === 'bottom',
    }
})
const action = ref(null)
const $emit = defineEmits(['change:action'])
function onAction() {
    $emit('change:action', action.value)
}
</script>
<style scoped>

</style>
