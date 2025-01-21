<template>
    <CategoryRow v-for="item in children" :category="item"/>
</template>
<script setup lang="ts">
import CategoryRow from "@Comp/Category/Row.vue";
import {computed, defineEmits, defineProps, ref} from "vue";

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
    categories: {
        type: Object,
        default: null,
    },
})
const $emit = defineEmits(['delete:category'])
const children = computed(() => {
    return props.category === null ? props.categories : props.category.children
})


</script>
