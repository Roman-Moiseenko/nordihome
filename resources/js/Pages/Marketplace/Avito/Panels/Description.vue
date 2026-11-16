<template>
    <el-tab-pane name="description">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-memo-pad"></i>
                <span> Описание</span>
            </span>
        </template>

        <div>
            <RichTextEditor v-model="description"/>
            <div class="mt-3 flex gap-2">
                <el-button v-if="hasChanges" type="primary" @click="onSave">Сохранить</el-button>
                <el-button v-if="hasChanges" @click="onCancel">Отмена</el-button>
            </div>
        </div>
    </el-tab-pane>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import RichTextEditor from "@Comp/Content/ContentBlock/RichTextEditor.vue";

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
})

let initialDescription = props.description ?? ''
const description = ref(initialDescription)

const hasChanges = computed(() => description.value !== initialDescription)

function onSave() {
    router.post(route('admin.marketplace.avito.product.description', { id: props.productId }), {
        description: description.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            initialDescription = description.value
        },
    })
}

function onCancel() {
    description.value = initialDescription
}
</script>

<style scoped>

</style>
