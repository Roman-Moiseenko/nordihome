import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useCatalogStore = defineStore('catalog', () => {

    const loaded = ref(false)
    const rooms = ref<any[]>([])
    const categories = ref<any[]>([])

    async function fetchData() {
        const [
            roomsRes, categoriesRes
        ] = await Promise.all([
            axios.get(route('admin.catalog.room.tree'), {withCredentials: true}),
            axios.get(route('admin.catalog.category.tree'), {withCredentials: true}),
        ])

        rooms.value = roomsRes.data
        categories.value = categoriesRes.data
        //console.log(rooms.value)
        //console.log('1*',categories.value)
    }

    ;(async () => {
        try {
            await fetchData()
            loaded.value = true
        } catch (error) {
            console.error('Failed to load auth data:', error)
            throw error
        }
    })()
    async function reload() {
        try {
            await fetchData()
        } catch (error) {
            console.error('Failed to reload auth data:', error)
            throw error
        }
    }

    /**
     * Рекурсивно превращает дерево в плоский список для фильтров
     */
    function flattenTree(tree: any[], depth: number = 0): { id: number, name: string }[] {
        const result: { id: number, name: string }[] = []
        for (const node of tree) {
            const prefix = depth > 0 ? '-'.repeat(depth) + ' ' : ''
            result.push({id: node.id, name: prefix + node.name})
            if (node.children && node.children.length > 0) {
                result.push(...flattenTree(node.children, depth + 1))
            }
        }
        return result
    }

    const categoriesForFilters = computed(() => flattenTree(categories.value))
    const roomsForFilters = computed(() => flattenTree(rooms.value))

    return {
        loaded,
        reload,
        rooms,
        categories,
        categoriesForFilters,
        roomsForFilters,
    }
})
