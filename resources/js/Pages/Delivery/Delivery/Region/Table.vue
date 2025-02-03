<template>
    <el-table
        :data="tableRegion"
        header-cell-class-name="nordihome-header"
        style="width: 100%; cursor: pointer;"
        @row-click="routeClick"
    >
        <el-table-column prop="address.address" label="Адрес"/>
        <el-table-column label="Клиент">
            <template #default="scope">
                {{ func.fullName(scope.row.recipient) }} <br>
                {{ func.phone(scope.row.phone) }}
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
        <el-table-column label="Транспортная компания" width="250">
            <template #default="scope">
                <span v-if="scope.row.delivery" >
                    {{ scope.row.delivery.cargo.name }}. Трек № <el-tag>{{ scope.row.delivery.track_number }}</el-tag>
                </span>
                <el-popover v-else
                            :visible="scope.row.visible_cargo"
                            placement="bottom-start" :width="246">
                    <template #reference>
                        <el-button type="primary" class="p-4 mr-2"
                                   @click.stop="scope.row.visible_cargo = !scope.row.visible_cargo">
                            Внести трек-номер
                            <el-icon class="ml-1">
                                <ArrowDown/>
                            </el-icon>
                        </el-button>
                    </template>
                    <el-input v-model="form.track" />
                    <el-radio-group v-model="form.company_id">
                        <el-radio v-for="item in companies" :value="item.id" :label="item.name" />
                    </el-radio-group>
                    <div class="mt-2">
                        <el-button @click="scope.row.visible_cargo = false">Отмена</el-button>
                        <el-button @click="scope.row.visible_cargo = false; setTrack(scope.row)"
                                   type="primary">Сохранить
                        </el-button>
                    </div>
                </el-popover>

            </template>
        </el-table-column>
        <el-table-column label="Доставлено">
            <template #default="scope">
                <Active :active="scope.row.completed" />
            </template>
        </el-table-column>
        <el-table-column label="Действия" align="right">
            <template #default="scope">
            </template>
        </el-table-column>
    </el-table>
</template>

<script setup lang="ts">
import {func} from '@Res/func.js'
import {defineProps, reactive, ref} from "vue";
import {Link, router} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    region: Array,
    drivers: Array,
    companies: Array,
})
const tableRegion = ref([...props.region])
const worker_id = ref(null)
const form = reactive({
    track: null,
    company_id: null,
})
function setTrack(row) {
    router.visit(route('admin.delivery.set-cargo', {expense: row.id}), {
        method: "post",
        data: form,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableRegion.value = [...page.props.region]
        }
    })
}
function setDriver(row) {
    router.visit(route('admin.delivery.set-driver', {expense: row.id}), {
        method: "post",
        data: {worker_id: worker_id.value},
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            tableRegion.value = [...page.props.region]
        }
    })
}
</script>
