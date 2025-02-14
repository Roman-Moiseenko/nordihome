<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Модификации товаров</h1>
        <div class="flex">
            <el-button type="primary" class="p-4 my-3" @click="onOpenDialog" ref="buttonRef">
                Создать Модификацию
            </el-button>
            <TableFilter :filter="filter" class="ml-auto" :count="filters.count">
                <el-input v-model="filter.name" placeholder="Товар, Модификация" class="mt-1"/>
            </TableFilter>
        </div>
        <div class="mt-2 p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
                :row-class-name="tableRowClassName"
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Название" width="380" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Кол-во товаров" width="180" align="center"/>
                <el-table-column prop="description" label="Атрибуты">
                    <template #default="scope">
                        <el-tag type="primary" effect="dark" v-for="item in scope.row.name_attributes" class="ml-1">
                            {{ item }}
                        </el-tag>
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
            :current_page="modifications.current_page"
            :per_page="modifications.per_page"
            :total="modifications.total"
        />
        <DeleteEntityModal name_entity="Модификацию"/>


        <el-dialog v-model="dialogCreate" title="Новая модификация" width="500">
            <el-form label-width="auto">
                <el-form-item label="Выберите базовый товар" label-position="top" class="mt-3">
                    <ModificationSearchProduct :action="'create'" @update:product_id="handleGetProduct"/>
                </el-form-item>
                <el-form-item label="Название модификации" label-position="top" class="mt-3">
                    <el-input id="name-modif" v-model="form.name" :placeholder="placeholder_name"/>
                </el-form-item>
                <el-form-item label="Атрибуты модификации" label-position="top" class="mt-3">
                    <el-select v-model="form.attributes" :placeholder="placeholder_attr" multiple>
                        <el-option v-for="item in attributes" :key="item.id" :value="item.id" :label="item.name"/>
                    </el-select>
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="dialogCreate = false">Отмена</el-button>
                    <el-button type="primary" @click="saveModification">Сохранить</el-button>
                </div>
            </template>
        </el-dialog>

    </el-config-provider>

</template>

<script setup lang="ts">
import ru from 'element-plus/dist/locale/ru.mjs'
import {useStore} from "@Res/store.js"
import Active from "@Comp/Elements/Active.vue";
import Pagination from "@Comp/Pagination.vue";
import TableFilter from "@Comp/TableFilter.vue";
import {Head, router} from "@inertiajs/vue3";
import {defineProps, inject, reactive, ref} from "vue";
import ModificationSearchProduct from "@Comp/Modification/SearchProduct.vue"
import {route} from "ziggy-js";
import axios from "axios";


const props = defineProps({
    modifications: Object,
    title: {
        type: String,
        default: 'Модификации товаров',
    },
    filters: Array,
})

const store = useStore();
const dialogCreate = ref(false)
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.modifications.data])
const filter = reactive({
    name: props.filters.name,
})

interface ListItem {
    id: Number
    name: String,
}
const placeholder_name = ref(null)
const placeholder_attr = ref(null)
const attributes = ref<ListItem[]>([])
const form = reactive({
    product_id: null,
    name: null,
    attributes: [],
})

function onOpenDialog() {
    form.product_id = null
    form.name = null
    form.attributes = []
    attributes.value = []
    placeholder_name.value = null
    placeholder_attr.value = null
    dialogCreate.value = true
}

function handleGetProduct(val) {
    form.product_id = val

    const getAttributes = route('admin.product.attr-modification', {product: form.product_id});

    axios.post(getAttributes).then(response => {
        console.log(response.data)
        if (response.data.error !== undefined) console.log(response.data.error)
        attributes.value = response.data
        placeholder_name.value = 'Введите название'
        placeholder_attr.value = 'Выберите 1-2 атрибута'
        document.getElementById('name-modif').focus()
    });
}

function saveModification() {
    router.post(route('admin.product.modification.store', form))
}

function routeClick(row) {
    router.get(route('admin.product.modification.show', {modification: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.modification.destroy', {modification: row.id}));
}
</script>
