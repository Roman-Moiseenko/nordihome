import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useGuideStore = defineStore('guide', () => {
    const loaded = ref(false)
    const groupAdditions = ref<any[]>([])
    const countries = ref<any[]>([])
    const markingType = ref<any[]>([])
    const measuring = ref<any[]>([])
    const VATs = ref<any[]>([])


    async function fetchData() {
        const [
            groupAdditionsRes, countriesRes, markingTypeRes, measuringRes, VATsRes
        ] = await Promise.all([
            axios.get(route('admin.guide.addition.list')),
            axios.get(route('admin.guide.country.list')),
            axios.get(route('admin.guide.marking-type.list')),
            axios.get(route('admin.guide.measuring.list')),
            axios.get(route('admin.guide.vat.list')),
        ])
        groupAdditions.value = groupAdditionsRes.data
        countries.value = countriesRes.data
        markingType.value = markingTypeRes.data
        measuring.value = measuringRes.data
        VATs.value = VATsRes.data
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
        countries,
        markingType,
        measuring,
        VATs,
    }
})
