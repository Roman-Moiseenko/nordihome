import { ref } from 'vue'

// Время жизни кэша данных панели — 5 минут.
const CACHE_TTL = 5 * 60 * 1000

// Общий кэш для всех панелей: panel => { data, at }
const cache = new Map()

/**
 * Универсальный загрузчик данных панели редактирования товара.
 *
 * - данные загружаются при активации панели;
 * - если данные уже загружались, повторный запрос не выполняется,
 *   пока не пройдёт 5 минут (CACHE_TTL);
 * - после сохранения кэш можно обновить через setCache() или сбросить
 *   через invalidate().
 *
 * @param {string} panel Ключ панели (например 'common')
 * @param {Function} loader Асинхронная функция загрузки данных
 */
export function useProductPanel(panel, loader) {
    const data = ref(null)
    const loading = ref(false)
    const loaded = ref(false)

    async function load(force = false) {
        const cached = cache.get(panel)
        const isFresh = cached && (Date.now() - cached.at) < CACHE_TTL

        if (!force && isFresh) {
            data.value = cached.data
            loaded.value = true
            return { data: cached.data, fromCache: true }
        }

        loading.value = true
        try {
            const result = await loader()
            cache.set(panel, { data: result, at: Date.now() })
            data.value = result
            loaded.value = true
            return { data: result, fromCache: false }
        } finally {
            loading.value = false
        }
    }

    /** Обновить кэш и текущие данные (после сохранения). */
    function setCache(result) {
        cache.set(panel, { data: result, at: Date.now() })
        data.value = result
        loaded.value = true
    }

    /** Принудительно сбросить кэш панели. */
    function invalidate() {
        cache.delete(panel)
    }

    return { data, loading, loaded, load, setCache, invalidate }
}
