import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useContentStore = defineStore('content', () => {

    const loaded = ref(false)
    const categories = ref<any[]>([])
    const widgets = ref<any[]>([])

    /** Варианты секций для ContentBlock */
    const sections = [
        { label: 'header', value: 'header' },
        { label: 'body', value: 'body' },
        { label: 'sidebar', value: 'sidebar' },
        { label: 'footer', value: 'footer' },
    ]

    async function fetchData() {
        const [
            categoriesRes, widgetsRes
        ] = await Promise.all([
            axios.get(route('admin.content.widget.categories')),
            axios.get(route('admin.content.widget.widgets')),

        ])

        categories.value = categoriesRes.data
        widgets.value = widgetsRes.data
        //console.log(categoriesRes.data)
        console.log(widgetsRes.data)
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
    return {
        loaded,
        reload,
        widgets,
        categories,
        sections,
    }
})
