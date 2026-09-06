import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useGuideStore = defineStore('guide', () => {
    const loaded = ref(false)
    const groupAdditions = ref<any[]>([])

    async function fetchData() {
        const [
            groupAdditionsRes,
        ] = await Promise.all([
            axios.get(route('admin.guide.addition.group')),
        ])
        groupAdditions.value = groupAdditionsRes.data
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
        groupAdditions,
    }
})
