<template>
    <CatalogRow v-for="item in children" :key="item.id" :item="item" :resource="resource"/>
</template>
<script setup lang="ts">
import CatalogRow from "@Comp/Catalog/Row.vue";
import {computed} from "vue";

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
    items: {
        type: Object,
        default: null,
    },
    resource: {
        type: String,
        default: 'category',
    },
})

const children = computed(() => {
    return props.item === null ? props.items : props.item.children
})

</script>
