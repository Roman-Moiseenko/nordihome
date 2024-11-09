<template>
    <i :class="font_awesome"></i>
    <span class="ml-1">{{ title }}</span>

</template>

<script setup>
const props = defineProps({
    title: String,
    font_awesome: String,
})

</script>
<style scoped>

</style>
