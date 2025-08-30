<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-coins"></i>
                <span> Финансовый учет</span>
            </span>
        </template>
        <div class="grid lg:grid-cols-6 grid-cols-1 divide-x">
            <div class="p-4 col-span-2">
                <el-descriptions :column="1" border>
                    <el-descriptions-item label="НДС">
                        <div v-show="!showEdit">
                            <span v-if="organization.vat_id"> {{ organization.vat_caption }} </span>
                            <el-button class="ml-2" type="warning" size="small" @click="showEdit = true">
                                <i class="fa-light fa-pen-to-square"></i>
                            </el-button>
                        </div>
                        <div v-show="showEdit">
                            <div>
                                <el-select
                                    v-model="vat_id"
                                    filterable
                                    clearable
                                    allow-create
                                    default-first-option
                                    :reserve-keyword="false"
                                    style="width: 240px"
                                >
                                    <el-option
                                        v-for="item in vat"
                                        :key="item.id"
                                        :label="item.name"
                                        :value="item.id"
                                    />
                                </el-select>
                                <el-button type="success" size="small" @click="saveVat" style="margin-left: 4px">
                                    <i class="fa-light fa-floppy-disk"></i>
                                </el-button>
                                <el-button type="info" size="small" @click="showEdit = false" style="margin-left: 4px">
                                    <i class="fa-light fa-xmark"></i>
                                </el-button>
                            </div>
                            <el-checkbox v-model="vat_all" label="Для всех товаров" :checked="vat_all"/>

                        </div>
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

const props = defineProps({
    organization: Array,
    vat: Array,
})
const showEdit = ref(false)
const vat_id = ref(props.organization.vat_id)
const vat_all = ref(true)

function saveVat() {
    if (vat_id.value === undefined) vat_id.value = null
    showEdit.value = false
    router.post(route('admin.accounting.organization.set-info',
        {organization: props.organization.id}),
        {
            vat_id: vat_id.value,
            vat_all: vat_all.value
        })
}


</script>
