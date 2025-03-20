<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Уведомления/Подписки</h1>
        <!-- Таблица -->
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="classes.TableActive"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Подписка" width="" show-overflow-tooltip />
                <el-table-column prop="title" label="Заголовок" />
                <el-table-column prop="description" label="Описание" width="300">
                </el-table-column>
                <el-table-column prop="active" label="Активна" width="160" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.active" />
                    </template>
                </el-table-column>
                <el-table-column prop="count_users" label="Подписчиков" width="200" align="center">
                </el-table-column>
                <el-table-column prop="listener" label="Клас"/>
                <el-table-column label="Действия" align="right" width="160">
                    <template #default="scope">
                        <el-button v-if="!scope.row.active"
                            size="small"
                            type="success"
                            @click.stop="onActivated(scope.row)">
                            Active
                        </el-button>
                        <el-button v-if="scope.row.active"
                            size="small"
                            type="warning"
                            @click.stop="onDraft(scope.row)">
                            Draft
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

    </el-config-provider>
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {useStore} from "@Res/store.js"
import TableFilter from '@Comp/TableFilter.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'
import {classes} from "@Res/className"

const props = defineProps({
    subscriptions: Array,
    title: {
        type: String,
        default: 'Список подписок',
    },
    filters: Array,
    type_pricing: Array,
})

const tableData = ref([...props.subscriptions])

function onActivated(row) {
    router.visit(route('admin.user.subscription.activated', {subscription: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}
function onDraft(row) {
    router.visit(route('admin.user.subscription.draft', {subscription: row.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
    })
}
function routeClick(row) {
    router.get(route('admin.user.subscription.show', {subscription: row.id}))
}

</script>

