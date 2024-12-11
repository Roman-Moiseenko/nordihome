<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-memo-pad"></i>
                <span> Описание</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item>

                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item>

                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>
            <!-- Колонка 3 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <el-form-item>

                    </el-form-item>
                    <!-- Повторить -->

                </el-form>
            </el-col>

        </el-row>
        <el-button v-if="!autoSave" type="primary" @click="onSave" class="mt-3">Сохранить</el-button>





    </el-tab-pane>
</template>

<script setup lang="ts">
import {reactive, ref, defineProps } from "vue";


const props = defineProps({
    product: Object,
})
const autoSave = ref(true)

const form = reactive({

})

function onSave() {

}

</script>

<style scoped>

</style>
