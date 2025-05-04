export interface IRowAccounting {
    completed: number,
    trashed: boolean,
}

export interface IRowActive {
    active: any
}

export interface IRowCompleted {
    completed: number
}

export interface IRowCostCurrency {
    cost_currency: number,
    quantity: number,
}
export interface IHonestItem {
    id: number,
    name: string,
    quantity: number,
    signs: string,
}
