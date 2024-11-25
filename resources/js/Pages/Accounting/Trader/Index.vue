<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Продавцы</h1>
        <div class="flex my-3">
            <el-popover :visible="visible_create" placement="bottom-start" :width="360">
                <template #reference>
                    <el-button type="primary" class="p-4" @click="visible_create = !visible_create" ref="buttonRef">
                        Добавить продавца
                        <el-icon class="ml-1">
                            <ArrowDown/>
                        </el-icon>
                    </el-button>
                </template>
                <el-form label-width="auto">
                    <el-form-item label="Название в CRM">
                        <el-input v-model="create.name" placeholder="Название в CRM" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="ИНН">
                        <el-input v-model="create.inn" placeholder="ИНН" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="БИК">
                        <el-input v-model="create.bik" placeholder="БИК" class="mt-1"/>
                    </el-form-item>
                    <el-form-item label="Расчетный счет">
                        <el-input v-model="create.account" placeholder="Расчетный счет" class="mt-1"/>
                    </el-form-item>
                </el-form>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button>
                    <el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>


        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название"/>

                <el-table-column label="Организация">
                    <template #default="scope">
                        <span v-if="scope.row.organization">{{ scope.row.organization.short_name }}</span>
                    </template>
                </el-table-column>

                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <!--el-button v-if="!scope.row.completed"
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button-->
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
</template>
<script lang="ts" setup>
import {reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'


const props = defineProps({
    traders: Object,
    title: {
        type: String,
        default: 'Список продавцов',
    },
})
const visible_create = ref(false)
const tableData = ref([...props.traders])
console.log(props.traders)
const create = reactive({
    name: null,
    inn: null,
    bik: null,
    account: null,
})

function createButton() {
    router.post(route('admin.accounting.trader.store', create))
}

function routeClick(row) {
    router.get(route('admin.accounting.trader.show', {trader: row.id}))
}
</script>
