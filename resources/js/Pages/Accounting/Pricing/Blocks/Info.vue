<template>
    <el-row :gutter="10">
        <el-col :span="12">
            <AccountingDocument v-model="info.document"
                                @update:modelValue="setInfo" v-model:saving="iSavingInfo" :edit="notEdit"/>
        </el-col>
        <el-col :span="12">
            <el-form label-width="auto">
                <el-form-item label="Основание">
                    <Link v-if="pricing.arrival_id" type="primary"
                          :href="route('admin.accounting.arrival.show', {arrival: pricing.arrival_id})" class="ml-2">
                        Приходная накладная № {{ pricing.arrival.number }} от {{ func.date(pricing.arrival.created_at) }}
                    </Link>
                </el-form-item>
            </el-form>
        </el-col>
    </el-row>
</template>

<script setup>
import {func} from '@Res/func.js'
import {computed, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import AccountingDocument from "@Comp/Pages/AccountingDocument.vue";

const props = defineProps({
    pricing: Object,
})
const iSavingInfo = ref(false)

const info = reactive({
    document: {
        number: props.pricing.number,
        created_at: props.pricing.created_at,
        incoming_number: props.pricing.incoming_number,
        incoming_at: props.pricing.incoming_at,
        comment: props.pricing.comment,
    },
})
const notEdit = computed(() => props.pricing.completed);

function setInfo() {
    iSavingInfo.value = true
    router.visit(route('admin.accounting.pricing.set-info', {pricing: props.pricing.id}), {
        method: "post",
        data: info,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}
</script>
