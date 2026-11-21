<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-map-location"></i>
                <span> Адрес</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 p-4">
            <div>
                <h2 class="font-medium">Юридический адрес</h2>
                <div class="mt-2">{{ organization.legalAddress }}</div>
            </div>
            <div>
                <div class="flex items-center">
                    <h2 class="font-medium">Фактический адрес</h2>
                    <span v-show="!showEdit" class="ml-2">
                        <el-button type="warning" size="small" @click="startEdit">
                            <i class="fa-light fa-pen-to-square"></i>
                        </el-button>
                    </span>
                    <span v-show="showEdit" class="ml-2">
                        <el-button type="success" size="small" @click="saveAddress">
                            <i class="fa-light fa-floppy-disk"></i>
                        </el-button>
                        <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                            <i class="fa-light fa-xmark"></i>
                        </el-button>
                    </span>
                </div>

                <div v-show="!showEdit" class="mt-2">
                    {{ organization.actualAddressFull }}
                </div>

                <el-form v-show="showEdit" label-width="120px" class="mt-2">
                    <el-form-item label="Индекс">
                        <el-input v-model="address.postalCode"/>
                    </el-form-item>
                    <el-form-item label="Регион">
                        <el-input v-model="address.region"/>
                    </el-form-item>
                    <el-form-item label="Город">
                        <el-input v-model="address.city"/>
                    </el-form-item>
                    <el-form-item label="Улица">
                        <el-input v-model="address.street"/>
                    </el-form-item>
                    <el-form-item label="Дом">
                        <el-input v-model="address.house"/>
                    </el-form-item>
                    <el-form-item label="Строение">
                        <el-input v-model="address.building"/>
                    </el-form-item>
                    <el-form-item label="Помещение">
                        <el-input v-model="address.flat"/>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup>
import {ref, reactive} from "vue";
import {router} from "@inertiajs/vue3";

const showEdit = ref(false)
const props = defineProps({
    organization: Object,
})

const address = reactive({
    postalCode: props.organization.actualAddress.postalCode ?? '',
    region: props.organization.actualAddress.region ?? '',
    city: props.organization.actualAddress.city ?? '',
    street: props.organization.actualAddress.street ?? '',
    house: props.organization.actualAddress.house ?? '',
    building: props.organization.actualAddress.building ?? '',
    flat: props.organization.actualAddress.flat ?? '',
})

function startEdit() {
    showEdit.value = true
}

function saveAddress() {
    showEdit.value = false
    router.put(route('admin.accounting.organization.update', {id: props.organization.id}), {address: address}, {preserveScroll: true})
}
</script>
