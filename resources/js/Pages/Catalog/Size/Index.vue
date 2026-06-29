<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Размеры товаров</h1>
        <div class="flex">
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                    <el-button type="primary" class="p-4 my-3" @click="visible_create = !visible_create" ref="buttonRef">
                        Создать категорию
                        <el-icon class="ml-1"><ArrowDown /></el-icon>
                    </el-button>
                </template>
                <el-input v-model="new_category" placeholder="Категория" class="mt-1"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="createButton" type="primary">Создать</el-button>
                </div>
            </el-popover>
        </div>

        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                @row-click="routeClick"
            >
                <el-table-column prop="name" label="Название категории"  width="280" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Размеры"  align="center">
                    <template #default="scope">
                        <el-tag type="info" v-for="item in scope.row.size">{{ item.name }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="Действия" align="right">
                    <template #default="scope">
                        <el-button v-if="!scope.row.completed"
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

        <pagination
            :current_page="categories.current_page"
            :per_page="categories.per_page"
            :total="categories.total"
        />

    </el-config-provider>
    <DeleteEntityModal name_entity="Категорию размеров" />
</template>
<script lang="ts" setup>
import {inject, reactive, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import Pagination from '@Comp/Pagination.vue'
import {func} from '@Res/func.js'
import ru from 'element-plus/dist/locale/ru.mjs'
import Active from '@Comp/Elements/Active.vue'

const props = defineProps({
    categories: Object,
    title: {
        type: String,
        default: 'Категории размеров товаров',
    },
    filters: Array,
})

const visible_create = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.categories.data])
/*const filter = reactive({
    product: props.filters.product,
    group: props.filters.group,

})*/
const new_category = ref('')


function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.size.destroy', {category: row.id}));
}
function createButton() {
    router.post(route('admin.product.size.store'), {name: new_category.value})
}
function routeClick(row) {
    console.log(row.id)
    router.get(route('admin.product.size.show', {category: row.id}))
}
</script>

