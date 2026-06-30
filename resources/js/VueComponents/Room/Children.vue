<template>
    <RoomRow v-for="item in children" :room="item"/>
</template>
<script setup lang="ts">
import RoomRow from "@Comp/Room/Row.vue";
import {computed, defineEmits, defineProps, ref} from "vue";

const props = defineProps({
    room: {
        type: Object,
        default: null,
    },
    categories: {
        type: Object,
        default: null,
    },
})
const $emit = defineEmits(['delete:room'])
const children = computed(() => {
    return props.room === null ? props.categories : props.room.children
})


</script>
