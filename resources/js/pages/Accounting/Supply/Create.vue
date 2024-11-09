<template>
    <Layout>
        <Head><title>{{ title }}</title></Head>
        <h1 class="font-medium text-xl mb-2">Новый заказ поставщику {{ distributor.name }}</h1>
        <el-form :model="form">
            <el-button type="primary" @click="onCreate">Создать</el-button>

          <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
            >
                <el-table-column prop="code" label="Артикул" width="160"/>
                <el-table-column prop="name" label="Товар" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Кол-во" width="100"/>
                <el-table-column prop="founded" label="Основание" width="260">
                    <template #default="scope">
                        <el-link v-if="scope.row.order_id" :href="route('admin.order.show', {order: scope.row.order_id})">{{ scope.row.founded }}</el-link>
                        <span v-else>{{ scope.row.founded }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="staff" label="Ответственный" show-overflow-tooltip/>
                <!-- Повторить -->
                <el-table-column label="Действия">
                    <template #default="scope">
                        <el-checkbox v-model="form.stacks" label="В заказ"
                                     type="checkbox" :key="scope.row.id"
                                     :value="scope.row.id" :disabled="scope.row.order_id"
                        />
                    </template>
                </el-table-column>
            </el-table>
        </div>
        </el-form>
    </Layout>
</template>

<script setup>
import Layout from "@Comp/Layout.vue";
import {Head, router} from "@inertiajs/vue3";
import {ref, reactive, inject} from "vue";

const props = defineProps({
    distributor: Number,
    stacks: Object,
    title: {
        type: String,
        default: 'Новый заказ. Товары со стека',
    },
})
const tableData = ref([...props.stacks])

const form = reactive({
    distributor: props.distributor.id,
    stacks: [...props.stacks.map(item => item.id)],
})

function onCreate() {
    router.post(route('admin.accounting.supply.store', form));
}

</script>
