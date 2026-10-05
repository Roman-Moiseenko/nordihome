import {ref, computed} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";

export const useAccountingStore = defineStore('accounting', () => {
    const loaded = ref(false)
    const traders = ref<any[]>([])
    const currencies = ref<any[]>([])

    async function fetchData() {
        const [
            listTradersRes,
            listCurrenciesRes,
        ] = await Promise.all([
            axios.get(route('admin.accounting.trader.list')),
            axios.get(route('admin.accounting.currency.list')),
        ])
        traders.value = listTradersRes.data
        currencies.value = listCurrenciesRes.data
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
        traders,
        currencies,
    }
})
