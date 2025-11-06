import {UploadFile} from "element-plus";

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

export interface IRowId {
    id: number
}

export interface IUploadFile {
    file: UploadFile,
    clear_file: Boolean,
}
export interface ISelectItem {
    value: any,
    label: string
}
