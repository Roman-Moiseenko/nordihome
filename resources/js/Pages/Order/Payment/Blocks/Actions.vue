<template>

    <div v-if="payment.manual">
        Провести/В работу
    </div>
</template>

<script setup lang="ts">
import {defineProps} from "vue";

const props = defineProps({
    payment: Object,
})
</script>
<style scoped>

</style>
