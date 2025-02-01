<template>
    <CategoryRow v-for="item in children" :category="item" @delete:category="handleDeleteEntity" :product_categories="product_categories"/>
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
    product_categories: Array,
})
const $emit = defineEmits(['delete:category'])
const children = computed(() => {
    return props.category === null ? props.categories : props.category.children
})
function handleDeleteEntity(id) {
    $emit('delete:category', id)
}

</script>
