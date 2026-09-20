<template>
    <div class="border rounded-lg bg-white shadow-sm mb-1">
        <div
            class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
            @click="collapsed = !collapsed"
        >
            <el-icon class="text-gray-400">
                <i :class="collapsed ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
            </el-icon>
            <span class="text-sm font-medium text-gray-600">{{ title }}</span>
        </div>
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import {ref} from "vue";

defineProps({
    title: {
        type: String,
        required: true,
    },
})

// По умолчанию все карточки свёрнуты
const collapsed = ref(true)
</script>
