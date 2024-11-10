<template>
    <i v-if="font_awesome" :class="font_awesome"></i>
    <span v-if="title" class="ml-1">{{ title }}</span>
    <span v-else>Нет Title</span>

</template>

<script setup>
const props = defineProps({
    title: String,
    font_awesome: String,
})

</script>
<style scoped>

</style>
