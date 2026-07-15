import { ref } from 'vue'
import api from '@Res/api'
import { route } from 'ziggy-js'

export interface WidgetInstanceData {
    id: number
    widgetId: number
    widgetName: string
    widgetSlug: string
    params: Record<string, any>
    title: string | null
    createdAt: string | null
    updatedAt: string | null
}

export interface ContentBlockData {
    id: number
    containerType: string
    containerId: number
    widgetInstanceId: number | null
    sort: number | null
    section: string | null
    caption: string | null
    widgetInstance: WidgetInstanceData | null
    createdAt: string | null
    updatedAt: string | null
}

export function useContentBlock() {
    const loading = ref(false)

    // ---- ContentBlock ----

    /** Создать ContentBlock */
    async function createBlock(payload: {
        container_type: string
        container_id: number
        caption?: string | null
        section?: string | null
    }): Promise<ContentBlockData> {
        loading.value = true
        try {
            const res = await api.post<ContentBlockData>(
                route('admin.content.content-blocks.store'),
                payload,
                { successMessage: 'Блок создан' },
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Получить ContentBlock по ID */
    async function getBlock(id: number): Promise<ContentBlockData> {
        loading.value = true
        try {
            const res = await api.get<ContentBlockData>(
                route('admin.content.content-blocks.show', { id }),
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Обновить ContentBlock (caption, section) */
    async function updateBlock(
        id: number,
        payload: { caption?: string | null; section?: string | null },
    ): Promise<ContentBlockData> {
        loading.value = true
        try {
            const res = await api.put<ContentBlockData>(
                route('admin.content.content-blocks.update', { id }),
                payload,
                { successMessage: 'Блок обновлён' },
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Сортировать ContentBlock */
    async function sortBlock(id: number, sort: number): Promise<void> {
        loading.value = true
        try {
            await api.post(
                route('admin.content.content-blocks.sort'),
                { id, sort },
                { successMessage: 'Порядок сортировки обновлён' },
            )
        } finally {
            loading.value = false
        }
    }

    /** Удалить ContentBlock */
    async function deleteBlock(id: number): Promise<void> {
        loading.value = true
        try {
            await api.delete(
                route('admin.content.content-blocks.destroy', { id }),
                null,
                { successMessage: 'Блок удалён' },
            )
        } finally {
            loading.value = false
        }
    }

    // ---- WidgetInstance ----

    /** Создать WidgetInstance (опционально с привязкой к ContentBlock) */
    async function createWidgetInstance(payload: {
        widget_id: number
        params?: Record<string, any>
        title?: string | null
        content_block_id?: number | null
    }): Promise<WidgetInstanceData> {
        loading.value = true
        try {
            const res = await api.post<WidgetInstanceData>(
                route('admin.content.widget-instances.store'),
                payload,
                { successMessage: 'Экземпляр виджета создан' },
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Получить WidgetInstance по ID */
    async function getWidgetInstance(id: number): Promise<WidgetInstanceData> {
        loading.value = true
        try {
            const res = await api.get<WidgetInstanceData>(
                route('admin.content.widget-instances.show', { id }),
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Обновить WidgetInstance */
    async function updateWidgetInstance(
        id: number,
        payload: { params?: Record<string, any>; title?: string | null },
    ): Promise<WidgetInstanceData> {
        loading.value = true
        try {
            const res = await api.put<WidgetInstanceData>(
                route('admin.content.widget-instances.update', { id }),
                payload,
                { successMessage: 'Экземпляр виджета обновлён' },
            )
            return res
        } finally {
            loading.value = false
        }
    }

    /** Удалить WidgetInstance */
    async function deleteWidgetInstance(id: number): Promise<void> {
        loading.value = true
        try {
            await api.delete(
                route('admin.content.widget-instances.destroy', { id }),
                null,
                { successMessage: 'Экземпляр виджета удалён' },
            )
        } finally {
            loading.value = false
        }
    }

    return {
        loading,
        createBlock,
        getBlock,
        updateBlock,
        sortBlock,
        deleteBlock,
        createWidgetInstance,
        getWidgetInstance,
        updateWidgetInstance,
        deleteWidgetInstance,
    }
}
