<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Группа атрибутов {{ group.name }}</h1>

        <div class="p-5 bg-white rounded-md">
            <el-form label-width="auto">
                <el-row :gutter="10">
                    <el-col :span="4">
                        <el-tooltip content="Изображение для группы" placement="top-start" effect="dark">
                            <PhotoDTO model-type="catalog.attribute-group" :entity-id="group.id" type="image" />
                        </el-tooltip>
                    </el-col>
                    <el-col :span="10">
                        <el-form-item label="Название группы">
                            <el-input v-model="info.name"/>
                        </el-form-item>
                        <el-form-item label="SVG">
                            <el-input v-model="info.svg" clearable type="textarea" :rows="2"/>
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
        </div>

        <div class="mt-4 p-5 bg-white rounded-md">
            <h2 class="font-medium text-lg mb-3">Атрибуты группы</h2>
            <el-table
                :data="group.attributes"
                header-cell-class-name="nordihome-header"
                style="width: 100%;"
            >
                <el-table-column prop="name" label="Атрибут" />
                <el-table-column prop="type_text" label="Тип" />
                <el-table-column label="Фильтр" width="160" align="center">
                    <template #default="scope">
                        <Active :active="scope.row.filter" />
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
</template>

<script setup lang="ts">
import {computed, reactive} from "vue";
import {Head, router} from "@inertiajs/vue3";
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from "@Comp/Elements/Active.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";

const props = defineProps({
    group: Object,
    title: {
        type: String,
        default: 'Карточка группы атрибутов',
    },
})

const initialInfo = {
    name: props.group?.name ?? '',
    svg: props.group?.svg ?? '',
}

const info = reactive({...initialInfo})

const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        if (JSON.stringify(info[key]) !== JSON.stringify(initialInfo[key])) return true
    }
    return false
})

function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    router.visit(
        route('admin.catalog.attribute-group.update', {id: props.group.id}), {
            method: 'put',
            data: {...info},
            preserveScroll: true,
            preserveState: false,
        }
    )
}
</script>

<style scoped>

</style>
