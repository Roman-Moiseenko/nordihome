import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useCatalogStore = defineStore('catalog', () => {

    const loaded = ref(false)
    const roomsTree = ref<any[]>([])
    const categoriesTree = ref<any[]>([])
    const brands = ref<any[]>([])
    const groups = ref<any[]>([])
    const promotions = ref<any[]>([])
    const series = ref<any[]>([])

    async function fetchData() {
        const [
            roomsRes, categoriesRes, brandsRes, groupsRes, promotionsRes, seriesRes,
        ] = await Promise.all([
            axios.get(route('admin.catalog.room.tree')),
            axios.get(route('admin.catalog.category.tree')),
            axios.get(route('admin.catalog.brand.list')),
            axios.get(route('admin.catalog.group.list')),
            axios.get(route('admin.discount.promotion.list')),
            axios.get(route('admin.catalog.series.list')),
        ])
        roomsTree.value = roomsRes.data
        categoriesTree.value = categoriesRes.data
        brands.value = brandsRes.data
        groups.value = groupsRes.data
        promotions.value = promotionsRes.data
        series.value = seriesRes.data
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

    const categories = computed(() => flattenTree(categoriesTree.value))
    const rooms = computed(() => flattenTree(roomsTree.value))

    return {
        loaded,
        reload,
        roomsTree,
        brands,
        groups,
        categoriesTree,
        series,
        promotions,
        categories,
        rooms,
    }
})
