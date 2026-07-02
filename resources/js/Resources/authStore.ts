import {ref} from 'vue'
import {defineStore} from 'pinia'
import axios from 'axios'
// @ts-ignore
import {route} from "ziggy-js";


export const useAuthStore = defineStore('auth', () => {

    const loaded = ref(false)
    const positions = ref<any[]>([])
    const permissions = ref<any[]>([])
    const roles = ref<any[]>([])
    const staffs = ref<any[]>([])
    //TODO списки сотрудников по positions - ?



    async function fetchData() {
        const [
            positionsRes, permissionsRes, rolesRes, staffsRes
        ] = await Promise.all([
            axios.get(route('admin.staff.positions')),
            axios.get(route('admin.role.permissions')),
            axios.get(route('admin.role.roles')),
            axios.get(route('admin.staff.groups')),
        ])

        positions.value = positionsRes.data
        permissions.value = permissionsRes.data
        roles.value = rolesRes.data
        staffs.value = staffsRes.data
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
        positions,
        permissions,
        roles,
        staffs,
        reload}
})
