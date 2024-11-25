<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Поставщик">
                    <EditField :field="distributor.name" @update:field="setInfo"/>
                </el-descriptions-item>
                <el-descriptions-item label="Текущий долг">
                    <el-tag :type="distributor.debit > 0 ? 'danger' : 'success'" size="large">
                    {{ func.price(distributor.debit, distributor.currency.sign) }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="Иностранный">
                    <Active :active="distributor.foreign" /> {{ distributor.currency.name }}
                </el-descriptions-item>
            </el-descriptions>
            <!-- //TODO Кол-во заказов, Сумма по всем заказам, Сумма всех платежей ??? Переходы на документы с фильтром по поставщику (Заказы, Платежи) -->
            <div class="mt-auto">
                <el-button type="success" @click="onSupply('all')">Заказать Все</el-button>
                <el-button type="primary" @click="onSupply('empty')">Отсутствующие</el-button>
                <el-button type="primary" plain @click="onSupply('min')">Минимальные</el-button>

            </div>
        </el-col>
        <el-col :span="8">
            <div v-for="contact in distributor.contacts" class="text-sm">
                <i class="fa-light fa-user-tie-hair text-sky-700 ml-2"></i> {{ func.fullName(contact.fullname)}}
                <i class="fa-light fa-circle-envelope text-sky-700 ml-2"></i> {{ contact.email }}
                <i class="fa-light fa-circle-phone text-sky-700 ml-2"></i> {{ func.phone(contact.phone) }}
            </div>
        </el-col>
        <el-col :span="8">
            <h2 class="font-medium">Организация</h2>
            <div v-for="item in distributor.organizations">
                <template v-if="item.pivot.default">
                    <el-tag  type="success">{{ item.short_name }}</el-tag>
                </template>
                <template v-else>
                    {{ item.short_name }}
                    <el-tooltip effect="dark" content="Назначить по-умолчанию" placement="top-start">
                        <el-button type="success" size="small" @click="defaultOrganization(item.id)" style="margin-left: 4px">
                            <i class="fa-light fa-check"></i>
                        </el-button>
                    </el-tooltip>
                    <el-button type="danger" size="small" @click="detachOrganization(item.id)" style="margin-left: 4px">
                        <i class="fa-light fa-trash"></i>
                    </el-button>
                </template>
                <Link type="primary" class="ml-3" :href="route('admin.accounting.organization.show', {organization: item.id})">
                    <i class="fa-light fa-right"></i>
                </Link>
            </div>
            <div class="mt-3">
                <SearchAttachOrganization
                    :route="route('admin.accounting.distributor.attach', {distributor: props.distributor.id})" />
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {ref, reactive} from "vue";
import {router, Link} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import EditField from "@Comp/Elements/EditField.vue";
import SearchAttachOrganization from "@Comp/Search/AttachOrganization.vue";

const props = defineProps({
    distributor: Object,
})

const showEdit = ref(false)
const editDistr = ref(false)

function onSupply(balance) {
    router.post(route('admin.accounting.distributor.supply', {distributor: props.distributor.id, balance: balance}))
}

function detachOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.accounting.distributor.detach', {distributor: props.distributor.id, organization: id}))
}
function defaultOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.accounting.distributor.default', {distributor: props.distributor.id, organization: id}))
}

function setInfo(val) {
    router.visit(route('admin.accounting.distributor.set-info', {distributor: props.distributor.id}), {
        method: "post",
        data: {
            name: val,
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            editDistr.value = false;
        }
    })
}
</script>
