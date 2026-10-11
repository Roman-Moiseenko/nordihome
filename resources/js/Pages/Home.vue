<template>
    <Head><title>{{ title }}</title></Head>
    <h1 class="font-medium text-xl">Режим тестирование</h1>
    <div class="mt-2 p-5 bg-white rounded-md">
        <h2 class="text-base font-medium mb-2">Последние изменения:</h2>
        <div v-for="(actions, date) in updates" class="mt-1">
            <h3 class="font-medium mt-3 mb-1">{{ date }}</h3>
            <ul>
                <li v-for="action in actions" class="text-sm"><span class="circle green"></span>{{ action }}</li>
            </ul>
        </div>
    </div>
</template>

<script setup lang="ts">
import { defineProps } from 'vue'
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    updates: Array,
    version: String,
    title: {
        default: 'Dashboard',
        type: String,
    }
})
</script>
