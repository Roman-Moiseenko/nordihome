import { ref, onMounted, onUnmounted } from 'vue'
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

// Подписки панелей на обновление после переноса между панелями
const refreshCallbacks = new Set()

function registerRefresh(callback) {
    refreshCallbacks.add(callback)
    return () => refreshCallbacks.delete(callback)
}

function requestRefresh(statuses) {
    refreshCallbacks.forEach(callback => callback(statuses))
}

export function useLeadPanel(status) {
    const leads = ref([])
    const loading = ref(false)

    const dialogCreateClient = ref(null)
    const dialogCreateOrder = ref(null)
    const dialogAddItem = ref(null)

    const canTransfer = TRANSFERABLE.includes(status)

    // Было открыто диалоговое окно — после успешного запроса обновляем панель
    let hasOpenedDialog = false

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

    // После переноса между панелями обновляем затронутые панели
    const offRefresh = registerRefresh((statuses) => {
        if (statuses.includes(status)) fetchLeads()
    })

    // Диалоговые окна, вызываемые из LeadInfo
    function onDialogClient(val) {
        hasOpenedDialog = true
        dialogCreateClient.value?.open(val)
    }

    function onDialogOrder(val) {
        hasOpenedDialog = true
        dialogCreateOrder.value?.open(val)
    }

    function onDialogItem(val) {
        hasOpenedDialog = true
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

        const from = dragFrom.value
        const leadId = dragItem.value.id

        router.visit(route(TRANSFER_ROUTES[status], { id: leadId }), {
            method: 'post',
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // Обновляем обе панели: исходную и целевую
                requestRefresh([from, status])
            },
        })

        dragItem.value = null
        dragFrom.value = null
    }

    // После успешного сохранения через диалоговое окно обновляем данные панели
    function onPageSuccess() {
        if (!hasOpenedDialog) return
        hasOpenedDialog = false
        fetchLeads()
    }

    const offSuccess = router.on('success', onPageSuccess)

    // Для панели new — опрос каждую минуту, добавляем только новые заявки
    let pollTimer = null

    function startPolling() {
        if (status !== 'new') return
        pollTimer = setInterval(() => {
            axios.get(route('admin.lead.leads', { status }))
                .then(response => {
                    const fresh = Array.isArray(response.data) ? response.data : []
                    const existingIds = new Set(leads.value.map(lead => lead.id))
                    const newLeads = fresh.filter(lead => !existingIds.has(lead.id))
                    if (newLeads.length > 0) {
                        leads.value = [...newLeads, ...leads.value]
                    }
                })
                .catch(error => {
                    console.error('Ошибка опроса заявок', error)
                })
        }, 60000)
    }

    onMounted(() => {
        fetchLeads()
        startPolling()
    })

    onUnmounted(() => {
        if (typeof offSuccess === 'function') offSuccess()
        if (typeof offRefresh === 'function') offRefresh()
        if (pollTimer) clearInterval(pollTimer)
    })

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
