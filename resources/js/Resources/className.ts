import { IRowAccounting, IRowActive, IRowCompleted, IRowCostCurrency } from "@Res/interface"

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
        if (row.completed === 0) return 'warning-row'
        return ''
    },
    TableCostCurrency: ({row}: { row: IRowCostCurrency }) => {
        if (row.cost_currency === 0 || row.quantity === 0) return 'error-row'
        return ''
    },

}
