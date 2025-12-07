<template>
    <CategoryRow v-for="item in children" :category="item" @delete:category="handleDeleteEntity"/>
</template>
<script setup lang="ts">
import CategoryRow from "./Row.vue";
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
function handleDeleteEntity(id) {
    $emit('delete:category', id)
}

</script>
