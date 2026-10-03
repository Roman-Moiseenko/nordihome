<template>
    <el-form label-width="auto">
        <el-row :gutter="10">
            <el-col :span="12">
                <el-form-item label="Название правила">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Вес (руб./кг)">
                    <el-input-number v-model="info.ratioWeight" :min="0" :precision="2" style="width: 100%"/>
                </el-form-item>

                <el-form-item label="Наценка ">
                    <el-input-number v-model="info.ratioMarkup" :min="0" :precision="3" :step="0.1"
                                     style="width: 100%"/>
                </el-form-item>
            </el-col>
            <el-col :span="12">
                <el-form-item label="Шаг округления">
                    <el-input-number v-model="info.roundingStep" :min="0" style="width: 100%"/>
                </el-form-item>

                <el-form-item label="Вычитание при округлении">
                    <el-input-number v-model="info.roundingSubtract" :min="0" style="width: 100%"/>
                </el-form-item>
                <el-form-item label="Активно">
                    <el-switch v-model="info.isActive"/>
                </el-form-item>
            </el-col>
        </el-row>
        <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
            Отмена
        </el-button>
        <el-button v-if="hasChanges" type="success" @click="onSetInfo">
            Сохранить
        </el-button>
    </el-form>
</template>

<script setup>
import {computed, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    rule: Object,
})

const iSavingInfo = ref(false)

const initialInfo = {
    name: props.rule?.name ?? '',
    ratioWeight: props.rule?.ratioWeight ?? 100,
    ratioMarkup: props.rule?.ratioMarkup ?? 1.300,
    roundingStep: props.rule?.roundingStep ?? 100,
    roundingSubtract: props.rule?.roundingSubtract ?? 10,
    isActive: Boolean(props.rule?.isActive),
}

const info = reactive({...initialInfo})

const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        const a = JSON.stringify(info[key])
        const b = JSON.stringify(initialInfo[key])
        if (a !== b) return true
    }
    return false
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    iSavingInfo.value = true;
    router.visit(
        route('admin.accounting.pricing-rule.update', {id: props.rule.id}), {
            method: "put",
            data: info,
            preserveScroll: true,
            preserveState: false,
            onSuccess: () => {
                iSavingInfo.value = false;
            },
            onError: () => {
                iSavingInfo.value = false;
            }
        }
    );
}
</script>
