<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-map-location"></i>
                <span> Адрес</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-6 grid-cols-1 divide-x">
            <div class="p-4 col-span-3">
                <h2>Юридический адрес</h2>
                <el-descriptions :column="2" border>
                    <el-descriptions-item label="Индекс">
                        {{ organization.legal_address.post }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Регион">
                        {{ organization.legal_address.region }}
                    </el-descriptions-item>
                    <el-descriptions-item label="Адрес">
                        {{ organization.legal_address.address }}
                    </el-descriptions-item>
                </el-descriptions>
            </div>
            <div class="p-4 col-span-3">
                <h2>Фактический адрес
                    <span v-show="!showEdit">
                        <el-button type="warning" size="small" @click="showEdit = true"><i class="fa-light fa-pen-to-square"></i></el-button>
                    </span>
                    <span v-show="showEdit">
                        <el-button type="success" size="small" @click="saveAddress">
                            <i class="fa-light fa-floppy-disk"></i>
                        </el-button>
                        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                            <i class="fa-light fa-xmark"></i>
                        </el-button>
                    </span>
                </h2>
                <el-descriptions :column="2" border>
                    <el-descriptions-item label="Индекс">
                        <span v-show="!showEdit">
                            {{ organization.actual_address.post }}
                        </span>
                        <span v-show="showEdit">
                            <el-input v-model="address.post" class="mr-2"/>
                        </span>
                    </el-descriptions-item>
                    <el-descriptions-item label="Регион">
                        <span v-show="!showEdit">
                        {{ organization.actual_address.region }}
                        </span>
                        <span v-show="showEdit">
                            <el-input v-model="address.region" class="mr-2"/>
                        </span>
                    </el-descriptions-item>
                    <el-descriptions-item label="Адрес">
                        <span v-show="!showEdit">
                        {{ organization.actual_address.address }}
                        </span>
                        <span v-show="showEdit">
                            <el-input v-model="address.address" class="mr-2"/>
                        </span>
                    </el-descriptions-item>
                </el-descriptions>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {ref, reactive} from "vue";
import {func} from '@Res/func.js'
import {router} from "@inertiajs/vue3";

const showEdit = ref(false)
const props = defineProps({
    organization: Array,
})

const address = reactive({
    post: props.organization.actual_address.post,
    region: props.organization.actual_address.region,
    address: props.organization.actual_address.address,
})
function saveAddress() {
    showEdit.value = false
    router.post(route('admin.accounting.organization.set-info', {organization: props.organization.id}), {address: address})
}
</script>
