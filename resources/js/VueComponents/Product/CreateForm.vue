<template>

    <div class="">
        <el-button @click="handleCancel">Отмена</el-button>
        <el-button type="primary" @click="handleSave">Сохранить</el-button>
    </div>
</template>

<script setup lang="ts">


const $emit = defineEmits(['close:save', 'close:cancel'])

function handleCancel() {
    $emit('close:cancel')
}

function handleSave() {
    //axios Сохраняем Товар, получаем id и отправляем в Событие
    $emit('close:save')
}

</script>

<style scoped>

</style>
