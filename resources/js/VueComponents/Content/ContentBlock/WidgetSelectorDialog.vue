<template>
    <el-dialog
        :model-value="visible"
        @update:model-value="$emit('close')"
        title="Выберите виджет"
        width="600px"
        class="widget-selector-dialog"
    >
        <el-tabs v-if="widgetGroupsKeys.length > 0" tab-position="left" class="min-h-[300px]">
            <el-tab-pane
                v-for="key in widgetGroupsKeys"
                :key="key"
                :label="widgetGroups[key].label"
                :name="key"
            >
                <div class="space-y-2">
                    <div
                        v-for="widget in widgetGroups[key].widgets"
                        :key="widget.id"
                        class="widget-item flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 cursor-pointer transition"
                        @click="selectWidget(widget)"
                    >
                        <div>
                            <div class="font-medium text-sm">{{ widget.name }}</div>
                            <div v-if="widget.description" class="text-xs text-gray-500 mt-0.5">
                                {{ widget.description }}
                            </div>
                        </div>
                        <el-button size="small" type="primary" plain>
                            Выбрать
                        </el-button>
                    </div>
                    <el-empty v-if="widgetGroups[key].widgets.length === 0" description="Нет виджетов в этой категории" />
                </div>
            </el-tab-pane>
        </el-tabs>

        <el-empty v-else description="Виджеты не загружены" />

        <template #footer>
            <el-button @click="$emit('close')">Отмена</el-button>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useContentStore } from '@Res/contentStore'

const props = defineProps<{
    visible: boolean
}>()

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'select', widget: any): void
}>()

const store = useContentStore()

// store.widgets — объект { "composite": { key, label, widgets: [...] }, ... }
const widgetGroups = computed(() => store.widgets as Record<string, { key: string; label: string; widgets: any[] }>)
const widgetGroupsKeys = computed(() => Object.keys(widgetGroups.value))

function selectWidget(widget: any) {
    emit('select', widget)
}
</script>

<style scoped>
.widget-selector-dialog :deep(.el-tabs__header) {
    margin-right: 0;
}
.widget-item:hover {
    border-color: var(--el-color-primary);
}
</style>
