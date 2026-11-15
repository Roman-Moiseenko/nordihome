<template>
    <Head><title>Общие параметры Авито</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Общие параметры Авито</h1>

        <div class="mt-3 p-5 bg-white rounded-md" >
            <el-form label-width="220px" @submit.prevent>
                <el-form-item v-for="(parameter, index) in parameters" :key="parameter.id" :label="parameter.name">
                    <el-select
                        v-if="parameter.variants.length"
                        v-model="form.parameters[index].value"
                        placeholder="Выберите значение"
                        clearable
                        style="width: 100%;"
                    >
                        <el-option
                            v-for="variant in parameter.variants"
                            :key="variant"
                            :value="variant"
                            :label="variant"
                        />
                    </el-select>
                    <RichTextEditor
                        v-else-if="parameter.isHtml"
                        v-model="form.parameters[index].value"
                        placeholder="HTML"
                    />
                    <el-input v-else v-model="form.parameters[index].value"/>
                </el-form-item>

                <el-button type="primary" @click="save">Сохранить</el-button>
            </el-form>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import { Head, router } from "@inertiajs/vue3";
import { reactive } from "vue";
import { route } from "ziggy-js";
import RichTextEditor from "../../../VueComponents/Content/ContentBlock/RichTextEditor.vue";

const props = defineProps({
    parameters: {
        type: Array,
        default: () => [],
    },
})

const form = reactive({
    parameters: props.parameters.map((p: any) => ({ id: p.id, value: p.value ?? '' })),
})

function save() {
    router.post(route('admin.marketplace.avito.settings.update'), form, {
        preserveScroll: true,
        preserveState: true,
    })
}
</script>
