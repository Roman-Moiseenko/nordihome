<template>
    <el-row :gutter="10">
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Причина">
                    <el-select v-model="info.reason" @change="setInfo" :disabled="disabled">
                        <el-option v-for="item in reasons" :key="item.value" :value="item.value" :label="item.label" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Комментарий">
                    <el-input v-model="info.comment"  @change="setInfo" :disabled="disabled"/>
                </el-form-item>
            </el-form>
        </el-col>
        <el-col :span="8">
            <Link type="primary" :href="route('admin.order.expense.show', {expense: refund.expense_id})">
                Распоряжение № {{ refund.expense.number }} от {{ func.date(refund.expense.created_at) }}
            </Link>
        </el-col>
        <el-col :span="8">

        </el-col>
    </el-row>

</template>

<script setup lang="ts">
import {computed, defineProps, reactive, ref} from "vue";
import {router, Link} from "@inertiajs/vue3";
import {func} from  "@Res/func.js"

const props = defineProps({
    refund: Object,
    reasons: Array,
})
const iSavingInfo = ref(false)
const disabled = computed(() => {
    return iSavingInfo.value || props.refund.completed !== 0
})
const info = reactive({
    reason: props.refund.reason,
    comment: props.refund.comment,
})

function setInfo() {
    iSavingInfo.value = true

    router.visit(route('admin.order.refund.set-info', {refund: props.refund.id}), {
        method: "post",
        data: info,
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            iSavingInfo.value = false;
        }
    })
}

</script>
