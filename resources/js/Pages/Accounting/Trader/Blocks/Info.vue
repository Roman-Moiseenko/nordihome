<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-descriptions :column="2" border class="mb-5">
                <el-descriptions-item label="Продавец">
                    <div v-show="!editTrader">
                        {{ trader.name }}
                        <el-button class="ml-2" type="warning" size="small" @click="editTrader = true">
                            <i class="fa-light fa-pen-to-square"></i>
                        </el-button>
                    </div>
                    <div v-show="editTrader">
                        <el-input v-model="info.name"/>
                        <el-button type="success" size="small" @click="setInfo">
                            <i class="fa-light fa-floppy-disk"></i>
                        </el-button>
                        <el-button type="info" size="small" @click="editTrader = false" style="margin-left: 4px">
                            <i class="fa-light fa-xmark"></i>
                        </el-button>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="По умолчанию">
                    <div v-show="!editTrader">
                        <Active :active="trader.default"/>
                    </div>
                    <div v-show="editTrader">
                        <el-checkbox v-model="info.default" label="По-умолчанию" :checked="trader.default"/>
                    </div>
                </el-descriptions-item>
            </el-descriptions>
        </el-col>
        <el-col :span="8">
            <div v-for="contact in trader.contacts" class="text-sm">
                <i class="fa-light fa-user-tie-hair text-sky-700 ml-2"></i> {{ func.fullName(contact.fullname) }}
                <i class="fa-light fa-circle-envelope text-sky-700 ml-2"></i> {{ contact.email }}
                <i class="fa-light fa-circle-phone text-sky-700 ml-2"></i> {{ func.phone(contact.phone) }}
            </div>
        </el-col>
        <el-col :span="8">
            <div v-for="item in trader.organizations">
                <template v-if="item.pivot.default">
                    <el-tag type="success">{{ item.short_name }}</el-tag>
                </template>
                <template v-else>
                    {{ item.short_name }}
                    <el-tooltip effect="dark" content="Назначить по-умолчанию" placement="top-start">
                        <el-button type="success" size="small" @click="defaultOrganization(item.id)"
                                   style="margin-left: 4px">
                            <i class="fa-light fa-check"></i>
                        </el-button>
                    </el-tooltip>
                    <el-button type="danger" size="small" @click="detachOrganization(item.id)" style="margin-left: 4px">
                        <i class="fa-light fa-trash"></i>
                    </el-button>
                </template>
                <Link type="primary" class="ml-3"
                      :href="route('admin.accounting.organization.show', {organization: item.id})">
                    <i class="fa-light fa-right"></i>
                </Link>
            </div>
            <div class="mt-3">
                <div v-show="!showEdit">
                    <el-button type="warning" size="small" @click="showEdit = true">Добавить</el-button>
                </div>
                <div v-show="showEdit" class="flex items-center">
                    <el-select v-model="organization" style="width: 260px;">
                        <el-option v-for="item in organizations" :key="item.id" :value="item.id"
                                   :label="item.short_name">
                        </el-option>
                    </el-select>
                    <el-button type="success" size="small" @click="attachOrganization" class="ml-3">
                        <i class="fa-light fa-floppy-disk"></i>
                    </el-button>
                    <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                        <i class="fa-light fa-xmark"></i>
                    </el-button>
                </div>
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {ref, reactive} from "vue";
import {router, Link} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    trader: Object,
    organizations: Array,

})

const showEdit = ref(false)
const editTrader = ref(false)
const organization = ref(null)


function attachOrganization() {
    showEdit.value = false;
    router.post(route('admin.accounting.trader.attach', {trader: props.trader.id}), {organization: organization.value})
}

function detachOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.accounting.trader.detach', {trader: props.trader.id, organization: id}))
}

function defaultOrganization(id) {
    showEdit.value = false;
    router.post(route('admin.accounting.trader.default', {trader: props.trader.id, organization: id}))
}

const info = reactive({
    name: props.trader.name,
    default: props.trader.default,
})

function setInfo() {
    //iSavingInfo.value = true
    router.visit(route('admin.accounting.trader.set-info', {trader: props.trader.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            editTrader.value = false;
        }
    })
}
</script>
