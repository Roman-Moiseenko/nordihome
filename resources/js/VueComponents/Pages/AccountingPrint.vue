<template>

</template>

<script setup>
import {inject, defineProps} from 'vue'
const props = defineProps({

})
//Список для печати/генерации
//{label, url}
const $printed = inject('$printed', [])
</script>

<style scoped>

</style>
