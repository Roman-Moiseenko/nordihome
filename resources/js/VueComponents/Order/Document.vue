<template>
    <div class="my-2 ml-1">
        <span v-if="usePage().url === document.link" class="ml-4">
            {{ document.name }}
        </span>
        <span v-else>
            <Active :active="document.completed"/>
            <Link :type="document.type" :href="document.link" class="ml-1">{{ document.name }}</Link>
        </span>
        <Document v-if="document.children && document.children.length > 0" v-for="_document in document.children" :document="_document" class="ml-5" />
    </div>

</template>

<script setup lang="ts">
import {inject, ref, defineProps} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import Document from "./Document.vue";

const props = defineProps({
    document: Object,
})

</script>
