<template>
    <template v-if="route_name">
        <Link v-if="vue" :href="route(route_name, undefined, false)" class="flex items-center w-full">
            <ItemText :font_awesome="font_awesome" :title="title"/>
        </Link>
        <a v-else :href="route(route_name, undefined, false)" class="flex items-center w-full">
            <ItemText :font_awesome="font_awesome" :title="title"/>
        </a>
    </template>
    <template v-else>
        <ItemText :font_awesome="font_awesome" :title="title"/>
    </template>
</template>

<script setup>
import {defineProps} from 'vue'
import {Link} from "@inertiajs/vue3";
import ItemText from "@Comp/Menu/ItemText.vue";
const props = defineProps({
    route_name: {
        default: null,
        type: String,
    },
    title: {
        default: null,
        type: String
    },
    font_awesome: {
        default: null,
        type: String
    },
    vue: {
        default: false,
        type: Boolean,
    },
})

</script>
