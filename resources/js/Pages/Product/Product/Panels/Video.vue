<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-video"></i>
                <span> Видеообзоры</span>
            </span>
        </template>
        <el-checkbox v-model="autoSave" :checked="autoSave">Автосохранение</el-checkbox>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="8">
                <el-form label-width="auto">
                    <!-- Повторить -->
                    <div v-for="(item, index) in form.videos" class="mt-2 bg-slate-200 rounded-md p-2 relative">
                        <div class="close-button-absolute">
                            <el-button type="danger" size="small" plain @click="onRemoveVideo(index)">X</el-button>
                        </div>
                        <el-form-item label="Ссылка на видео" label-position="top" class="">
                            <el-input v-model="item.url" @change="onAutoSave" :disabled="isSaving"/>
                        </el-form-item>
                        <el-form-item label="Заголовок" label-position="top" class="">
                            <el-input v-model="item.caption" @change="onAutoSave" :disabled="isSaving" />
                        </el-form-item>
                        <el-form-item label="Описание" label-position="top" class="">
                            <el-input v-model="item.description" @change="onAutoSave" :disabled="isSaving" type="textarea" rows="3" resize="none"/>
                        </el-form-item>
                    </div>
                    <el-button type="success" @click="onAddVideo" class="mt-1">Добавить</el-button>

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
import {reactive, ref, defineProps } from "vue"
import {router} from "@inertiajs/vue3"

const props = defineProps({
    product: Object,
    errors: Object,
})
const autoSave = ref(true)
const isSaving = ref(false)
const form = reactive({
    videos: [...props.product.videos]
})

function onAutoSave() {
    if (autoSave.value === false) return;
    onSave()
}
function onSave() {
    isSaving.value = true;
    router.visit(route('admin.product.edit.video', {product: props.product.id}), {
        method: "post",
        data: form,
        preserveState: true,
        preserveScroll: true,
        onSuccess: page => {
            isSaving.value = false
            form.videos = [...page.props.product.videos]
        },
        onError: page => {
            isSaving.value = false

        },
    })
}

function onAddVideo() {
    form.videos.push({
        url: 'https://',
        caption: null,
        description: null,
    })
    onAutoSave()
}
function onRemoveVideo(index) {
    form.videos.splice(index, 1)
    onAutoSave()
}
</script>

<style lang="scss">
.close-button-absolute {
    position: absolute;
    top: 8px;
    right: 8px;
}
</style>
