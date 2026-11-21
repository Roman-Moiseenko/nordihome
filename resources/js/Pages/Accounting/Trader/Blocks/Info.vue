<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <PhotoDTO model-type="accounting.trader" :entity-id="trader.id" type="image" />

            <el-descriptions :column="1" border class="mb-5">
                <el-descriptions-item label="Продавец">
                    <div v-show="!editTrader">
                        {{ trader.name }}
                    </div>
                    <div v-show="editTrader">
                        <el-input v-model="info.name"/>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="Описание">
                    <div v-show="!editTrader">
                        {{ trader.description }}
                    </div>
                    <div v-show="editTrader">
                        <el-input v-model="info.description" type="textarea" rows="3"/>
                    </div>
                </el-descriptions-item>
                <el-descriptions-item label="По умолчанию">
                    <div v-show="!editTrader">
                        <Active :active="trader.default"/>
                    </div>
                    <div v-show="editTrader">
                        <el-checkbox v-model="info.default" :disabled="trader.default">По-умолчанию</el-checkbox>
                    </div>
                </el-descriptions-item>
            </el-descriptions>

            <div v-show="!editTrader">
                <el-button class="ml-2" type="warning" size="small" @click="editTrader = true">
                    <i class="fa-light fa-pen-to-square mr-2"></i> Изменить
                </el-button>
            </div>
            <div v-show="editTrader">
                <el-button type="success" size="small" @click="updateTrader">
                    <i class="fa-light fa-floppy-disk mr-2"></i> Сохранить
                </el-button>
                <el-button type="info" size="small" @click="editTrader = false" style="margin-left: 4px">
                    <i class="fa-light fa-xmark mr-2"></i> Отмена
                </el-button>
            </div>
        </el-col>

        <el-col :span="16">
            <h2 class="font-medium">Организации</h2>
            <div v-for="item in trader.organizations" :key="item.id" class="flex items-center my-1">
                <template v-if="item.default">
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
                      :href="route('admin.accounting.organization.show', {id: item.id})">
                    <i class="fa-light fa-right"></i>
                </Link>
            </div>
            <div class="mt-3">
                <SearchAttachOrganization
                    :route="route('admin.accounting.trader.attach', {id: trader.id})" />
            </div>
        </el-col>
    </el-row>
</template>

<script setup>
import {reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import Active from "@Comp/Elements/Active.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";
import SearchAttachOrganization from "@Comp/Search/AttachOrganization.vue";

const props = defineProps({
    trader: Object,
})

const editTrader = ref(false)

const info = reactive({
    name: props.trader.name,
    default: !!props.trader.default,
    description: props.trader.description,
})

function detachOrganization(id) {
    router.visit(route('admin.accounting.trader.detach', {id: props.trader.id}), {
        method: 'post',
        data: {organization: id},
        preserveScroll: true,
        preserveState: true,
    })
}

function defaultOrganization(id) {
    router.visit(route('admin.accounting.trader.update', {id: props.trader.id}), {
        method: 'put',
        data: {organization: id},
        preserveScroll: true,
        preserveState: true,
    })
}

function updateTrader() {
    router.visit(route('admin.accounting.trader.update', {id: props.trader.id}), {
        method: 'put',
        data: info,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            editTrader.value = false;
        }
    })
}
</script>
