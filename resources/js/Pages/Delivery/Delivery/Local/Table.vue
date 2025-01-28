<template>
    <el-table
        :data="tableLocal"
        header-cell-class-name="nordihome-header"
        style="width: 100%; cursor: pointer;"
        @row-click="routeClick"
        row-key="id"
        :expand-row-keys="expands"
    >
        <el-table-column type="expand">
            <template #default="scope">
                <div v-for="period in scope.row.periods">
                    <div v-if="period.expenses.length > 0">
                        <el-tag size="large" type="warning" effect="dark" class="mt-2">
                            {{ period.time_text }}
                        </el-tag>
                        <el-table :data="period.expenses" :border="true"
                                  header-cell-class-name="light-header"
                        >
                            <el-table-column prop="address.address" label="Адрес"/>
                            <el-table-column label="Клиент">
                                <template #default="scope">
                                    {{ func.fullName(scope.row.recipient) }} <br>
                                    {{ func.phone(scope.row.phone) }}
                                </template>
                            </el-table-column>
                            <el-table-column label="Доставщик" width="250">
                                <template #default="scope">
                                    <el-tag v-if="scope.row.driver">{{
                                            func.fullName(scope.row.driver.fullname)
                                        }}
                                    </el-tag>
                                    <el-popover v-else
                                                :visible="scope.row.visible_driver"
                                                placement="bottom-start" :width="246">
                                        <template #reference>
                                            <el-button type="primary" class="p-4 mr-2"
                                                       @click.stop="scope.row.visible_driver = !scope.row.visible_driver">
                                                Назначить
                                                <el-icon class="ml-1">
                                                    <ArrowDown/>
                                                </el-icon>
                                            </el-button>
                                        </template>
                                        <el-select v-model="worker_id">
                                            <el-option v-for="item in drivers" :value="item.id"
                                                       :label="func.fullName(item.fullname)"/>
                                        </el-select>
                                        <div class="mt-2">
                                            <el-button @click="scope.row.visible_driver = false">Отмена</el-button>
                                            <el-button @click="scope.row.visible_driver = false; setDriver(scope.row)"
                                                       type="primary">Назначить
                                            </el-button>
                                        </div>
                                    </el-popover>

                                </template>
                            </el-table-column>
                            <el-table-column label="Сборка" width="250">
                                <template #default="scope">
                                    <div v-if="scope.row.is_assemble">
                                        <el-tag v-if="scope.row.assemble">
                                            {{ func.fullName(scope.row.assemble.fullname) }}
                                        </el-tag>
                                        <el-popover v-else
                                                    :visible="scope.row.visible_assemble"
                                                    placement="bottom-start" :width="246">
                                            <template #reference>
                                                <el-button type="primary" class="p-4 mr-2"
                                                           @click.stop="scope.row.visible_assemble = !scope.row.visible_assemble">
                                                    Назначить
                                                    <el-icon class="ml-1">
                                                        <ArrowDown/>
                                                    </el-icon>
                                                </el-button>
                                            </template>
                                            <el-select v-model="worker_id">
                                                <el-option v-for="item in assembles" :value="item.id"
                                                           :label="func.fullName(item.fullname)"/>
                                            </el-select>
                                            <div class="mt-2">
                                                <el-button @click="scope.row.visible_assemble = false">Отмена
                                                </el-button>
                                                <el-button
                                                    @click="scope.row.visible_assemble = false; setAssemble(scope.row)"
                                                    type="primary">Назначить
                                                </el-button>
                                            </div>
                                        </el-popover>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column label="Распоряжение" width="180">
                                <template #default="scope">
                                    <Link type="primary"
                                          :href="route('admin.order.expense.show', {expense: scope.row.id})">
                                        № {{ scope.row.number }} от {{ func.date(scope.row.created_at) }}
                                    </Link>
                                </template>
                            </el-table-column>
                            <el-table-column label="Действия">
                                <template #default="scope">
                                    <el-button type="success" v-if="scope.row.driver" @click="onCompleted(scope.row)">Выдано</el-button>
                                    <el-button type="warning" @click="openDialogPeriod(scope.row)">Сменить дату</el-button>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </div>
            </template>
        </el-table-column>
        <el-table-column label="Дата" width="120">
            <template #default="scope">
                <span class="font-medium text-lg">{{ func.date(scope.row.date_at) }}</span>
            </template>
        </el-table-column>
        <el-table-column prop="count" label="Доставки в работе" align="center" width="150">
            <template #default="scope">
                <el-tag type="success" effect="dark" size="large">{{ scope.row.count }}</el-tag>
            </template>
        </el-table-column>
        <el-table-column label="Доставка" width="150" align="center">
            <template #default="scope">
                <Active :active="scope.row.is_drivers"/>
            </template>
        </el-table-column>

        <el-table-column label="Действия" align="right">
            <template #default="scope">
            </template>
        </el-table-column>
    </el-table>

    <el-dialog v-model="dialogPeriod" title="Назначить новую дату отгрузки" width="400">
        <el-date-picker
            v-model="form_calendar.date_at"
            :disabled-date="disabledDate"
            placeholder="Выберите дату доставки"
            @change="findPeriod" />
        <div class="mt-3" v-if="periods.length > 0">
            <h2>Время доставки</h2>
            <el-radio-group v-model="form_calendar.period_id" style="display: block;" >
                <el-row v-for="period in periods" class="mt-2">
                    <el-radio border :value="period.id" >
                        {{ period.time_text }} ({{ period.free_weight }} кг, {{ period.free_volume }} м3)
                    </el-radio>
                </el-row>

            </el-radio-group>
        </div>
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="dialogPeriod = false">Отмена</el-button>
                <el-button type="primary" @click="setPeriod">
                    Сохранить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import {func} from '@Res/func.js'
import {defineProps, reactive, ref} from "vue";
import {Link, router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import axios from "axios";

const props = defineProps({
    local: Array,
    drivers: Array,
    assembles: Array,
    incomplete: {
        type: Boolean,
        default: false,
    }
})
const expands = ref([])
const tableLocal = ref([...props.local])
const worker_id = ref(null)
const expense_id = ref(null)
const dialogPeriod = ref(false)

function routeClick(row) {
    if (expands.value.includes(row.id)) {
        expands.value = expands.value.filter(val => val !== row.id);
    } else {
        expands.value = [];// Add this code to achieve the wind piano mode, delete the code to cancel the wind piano mode
        expands.value.push(row.id);
    }
}
function setDriver(row) {
    router.visit(route('admin.delivery.set-driver', {expense: row.id}), {
        method: "post",
        data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            if (props.incomplete) {
                tableLocal.value = [...page.props.incomplete]
            } else {
                tableLocal.value = [...page.props.local]
            }
        }
    })
}
function setAssemble(row) {
    router.visit(route('admin.delivery.set-assemble', {expense: row.id}), {
        method: "post",
        data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            if (props.incomplete) {
                tableLocal.value = [...page.props.incomplete]
            } else {
                tableLocal.value = [...page.props.local]
            }
        }
    })
}
function onCompleted(row) {
    router.visit(route('admin.delivery.completed', {expense: row.id}), {
        method: "post",
        //data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            if (props.incomplete) {
                tableLocal.value = [...page.props.incomplete]
            } else {
                tableLocal.value = [...page.props.local]
            }
        }
    })
}
//Смена даты
const disabledDate = (time: Date) => {
    return time.getTime() <= Date.now()
}
const form_calendar = reactive({
    date_at: null,
    period_id: null,
})
const periods = ref([])
function findPeriod() {
    form_calendar.date_at = func.date(form_calendar.date_at)
    axios.post(route('admin.delivery.calendar.get-day'), {date: form_calendar.date_at}).then( result => {
        console.log(result.data)
        if (result.data.length > 0) {
            periods.value = [...result.data]
        } else {
            periods.value = []
        }
    })

}
function openDialogPeriod(row) {
    expense_id.value = row.id
    dialogPeriod.value = true;
}
function setPeriod() {
    router.visit(route('admin.delivery.set-period', {expense: expense_id.value}), {
        method: "post",
        data: form_calendar,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            dialogPeriod.value = false
            if (props.incomplete) {
                tableLocal.value = [...page.props.incomplete]
            } else {
                tableLocal.value = [...page.props.local]
            }
        }
    })
}

</script>
