<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Размерные сетки</h1>

        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="340">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать сетку
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>

                <el-input v-model="new_name" placeholder="Название сетки" class="mt-1"/>

                <el-select
                    v-model="new_dimensions"
                    multiple
                    :multiple-limit="2"
                    placeholder="Измерения"
                    class="mt-2"
                    style="width: 100%"
                >
                    <el-option
                        v-for="dimension in catalogStore.sizeGridDimensions"
                        :key="dimension.code"
                        :label="dimension.name"
                        :value="dimension.code"
                    />
                </el-select>

                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button>
                    <el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="name" label="Название сетки" min-width="220" show-overflow-tooltip/>
                <el-table-column label="Измерения" min-width="200">
                    <template #default="scope">
                        <el-tag v-for="dimension in scope.row.dimensions" :key="dimension.code" class="mr-1">
                            {{ dimension.name }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="valuesCount" label="Значений" width="120" align="center"/>
                <el-table-column label="Действия" align="right" width="120">
                    <template #default="scope">
                        <el-button
                            size="small"
                            type="danger"
                            @click.stop="handleDeleteEntity(scope.row)"
                        >
                            Delete
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
    </el-config-provider>
    <DeleteEntityModal name_entity="Размерную сетку" />
</template>

<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import {useCatalogStore} from "@Res/catalogStore.ts"
import ru from 'element-plus/dist/locale/ru.mjs'

const props = defineProps({
    sizeGrids: Array,
    title: {
        type: String,
        default: 'Размерные сетки',
    },
})

const store = useStore();
const catalogStore = useCatalogStore();
const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...(props.sizeGrids || [])])

const new_name = ref('')
const new_dimensions = ref([])

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.catalog.size-grid.destroy', {id: row.id}));
}

function createButton() {
    router.post(route('admin.catalog.size-grid.store'), {
        name: new_name.value,
        dimensions: new_dimensions.value,
    })
}

function routeClick(row) {
    router.get(route('admin.catalog.size-grid.show', {id: row.id}))
}
</script>
