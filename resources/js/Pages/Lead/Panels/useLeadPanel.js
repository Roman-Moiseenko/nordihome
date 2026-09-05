import { ref, onMounted } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Ширина одной панели
export const PANEL_SIZE = 250

// Общее состояние перетаскивания между панелями
const dragItem = ref(null)
const dragFrom = ref(null)

// Перетаскивать LeadInfo можно только между этими панелями
const TRANSFERABLE = ['new', 'in_work', 'not_decided']

// Каждая панель использует свой маршрут изменения статуса
const TRANSFER_ROUTES = {
    new: 'admin.lead.return-new',
    in_work: 'admin.lead.in-work',
    not_decided: 'admin.lead.not-decided',
}

export function useLeadPanel(status) {
    const leads = ref([])
    const loading = ref(false)

    const dialogCreateClient = ref(null)
    const dialogCreateOrder = ref(null)
    const dialogAddItem = ref(null)

    const canTransfer = TRANSFERABLE.includes(status)

    function fetchLeads() {
        loading.value = true
        axios.get(route('admin.lead.leads', { status }))
            .then(response => {
                leads.value = Array.isArray(response.data) ? response.data : []
            })
            .catch(error => {
                console.error('Ошибка загрузки заявок', error)
            })
            .finally(() => {
                loading.value = false
            })
    }

    // Диалоговые окна, вызываемые из LeadInfo
    function onDialogClient(val) {
        dialogCreateClient.value?.open(val)
    }

    function onDialogOrder(val) {
        dialogCreateOrder.value?.open(val)
    }

    function onDialogItem(val) {
        dialogAddItem.value?.open(val)
    }

    // Drag & Drop
    function onDragStart(item) {
        if (!canTransfer) return
        dragItem.value = item
        dragFrom.value = status
    }

    function onDropOver() {
        console.log('onDropOver', status)
    }

    function onDropList() {
        if (!canTransfer) return
        if (dragFrom.value === status) {
            dragItem.value = null
            dragFrom.value = null
            return
        }
        // Из панели new можно перетаскивать только в in_work
        if (dragFrom.value === 'new' && status !== 'in_work') {
            dragItem.value = null
            dragFrom.value = null
            return
        }
        if (!dragItem.value) return

        router.visit(route(TRANSFER_ROUTES[status], { id: dragItem.value.id }), {
            method: 'post',
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {},
        })

        dragItem.value = null
        dragFrom.value = null
    }

    onMounted(fetchLeads)

    return {
        leads,
        loading,
        dialogCreateClient,
        dialogCreateOrder,
        dialogAddItem,
        onDialogClient,
        onDialogOrder,
        onDialogItem,
        onDragStart,
        onDropOver,
        onDropList,
        canTransfer,
    }
}
