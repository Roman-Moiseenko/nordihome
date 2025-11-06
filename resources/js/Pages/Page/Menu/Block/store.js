import {defineStore} from 'pinia'
import { ref } from "vue"

export const menuStore = defineStore('menu_item_store', {
    state: () => ({
        reload: false,
    }),
    getters: {
        getReloading: (state) => state.reload,
    },
    actions: {
        beforeReload() {
            this.reload = true;
        },
        afterReload() {
            this.reload = false;
        }
    },
})
