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
                <el-button size="small" type="primary" link @click="$emit('edit', block.id)">
                    Изменить
                </el-button>
                <el-button size="small" type="danger" text @click="$emit('delete', block.id)">
                    Удалить блок
                </el-button>
            </div>
        </div>

        <!-- Контент блока (сворачивается) -->
        <div v-show="!collapsed" class="border-t px-3 py-3">
            <!-- Есть виджет — показываем его компонент -->
            <div v-if="block.widgetInstance" class="widget-container">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500">
                        Виджет: <strong>{{ block.widgetInstance.widgetName }}</strong>
                        (ID: {{ block.widgetInstance.id }})
                    </span>
                    <el-button
                        size="small"
                        type="danger"
                        plain
                        @click="$emit('removeWidget', block.id)"
                    >
                        Удалить виджет
                </el-button>
                </div>
            </div>

            <!-- Нет виджета — показываем кнопку добавить -->
            <div v-else class="flex items-center justify-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                <el-button
                    type="primary"
                    size="default"
                    @click="$emit('addWidget', block.id)"
                >
                    + Добавить Виджет
                </el-button>
    </div>
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
    (e: 'addWidget', id: number): void
    (e: 'removeWidget', id: number): void
    (e: 'edit', id: number): void
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
