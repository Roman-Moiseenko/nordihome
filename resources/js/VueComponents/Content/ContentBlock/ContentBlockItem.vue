<template>
    <div class="border rounded-lg bg-white shadow-sm" :data-id="block.id">
        <!-- Заголовок блока (всегда виден) -->
        <div
            class="flex items-center gap-2 px-3 py-2 cursor-pointer select-none"
            @click="$emit('toggle', block.id)"
        >
            <span class="drag-handle cursor-grab text-gray-400 hover:text-gray-600">
                ⠿
            </span>

            <el-icon class="text-gray-400" :class="{ 'rotate-90': collapsed }">
                <i :class="collapsed ? 'fa-light fa-chevron-right' : 'fa-light fa-chevron-down'" />
            </el-icon>

            <span class="text-sm font-medium text-gray-600">
                Блок #{{ index + 1 }}
            </span>

            <span class="text-sm text-gray-700 truncate max-w-[200px]" :title="block.caption || ''">
                {{ block.caption || '' }}
            </span>

            <el-tag v-if="block.section" size="small" type="info">
                {{ block.section }}
            </el-tag>

            <el-tag v-if="block.widgetInstance" size="small" type="success">
                {{ block.widgetInstance.widgetName }}
            </el-tag>

            <div class="ml-auto flex gap-1" @click.stop>
                <el-button size="small" type="danger" text @click="$emit('delete', block.id)">
                    Удалить
                </el-button>
            </div>
        </div>

        <!-- Контент блока (сворачивается) -->
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <!-- Заголовок внутри -->
            <div class="text-base font-medium mb-1">
                {{ block.caption || 'Без заголовка' }}
            </div>

            <!-- JSON данных блока (заглушка, потом заменим на виджет) -->
            <pre class="text-xs bg-gray-50 p-2 rounded overflow-auto max-h-40 mt-2">{{ JSON.stringify(block, null, 2) }}</pre>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    block: any
    index: number
    collapsed: boolean
}>()

defineEmits<{
    (e: 'toggle', id: number): void
    (e: 'delete', id: number): void
}>()
</script>

<style scoped>
.drag-handle {
    cursor: grab;
}
.drag-handle:active {
    cursor: grabbing;
}
.rotate-90 {
    transform: rotate(90deg);
}
</style>
