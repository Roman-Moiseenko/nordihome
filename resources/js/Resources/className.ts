import { IRowAccounting, IRowActive, IRowCompleted, IRowCostCurrency, IRowRead } from "@Res/interface"

export const classes = {
    TableAccounting: ({row}: { row: IRowAccounting }) => {
        if (row.trashed === true) return 'danger-row'
        if (row.completed === 0) return 'warning-row'
        return ''
    },

    TableActive: ({row}: { row: IRowActive }) => {
        if (row.active === 0) return 'warning-row'
        return ''
    },
    TableCompleted: ({row}: { row: IRowCompleted }) => {
        if (row.status === 'cancelled') return 'gray-row'
        if (row.status === 'new') return 'danger-row'
        if (row.status === 'draft') return 'warning-row'
        if (row.status === 'awaiting') return 'primary-row'
        if (row.status === 'completed') return 'success-row'
        return ''
    },
    TableCostCurrency: ({row}: { row: IRowCostCurrency }) => {
        if (row.cost_currency === 0 || row.quantity === 0) return 'error-row'
        return ''
    },
    TableRead: ({row}: { row: IRowRead }) => {
        if (row.read === 0) return 'warning-row'
        return ''
    },
}
