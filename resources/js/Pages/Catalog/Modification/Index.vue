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
                @row-click="routeClick"
                v-loading="store.getLoading"
            >
                <el-table-column prop="image" label="IMG" width="60">
                    <template #default="scope">
                        <img v-if="scope.row.image" :src="scope.row.image" style="width: 100%">
                    </template>
                </el-table-column>
                <el-table-column prop="name" label="Название" width="380" show-overflow-tooltip/>
                <el-table-column prop="quantity" label="Кол-во товаров" width="180" align="center"/>
                <el-table-column prop="attributes" label="Атрибуты">
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
                <el-form-item label="Выберите первичный товар" label-position="top" class="mt-3">
                    <ModificationSearchProduct
                        :action="'create'"
                        @update:productId="onProductId"
                        @update:name="onProductName"
                        @update:attributes="onProductAttributes"
                    />
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
                    <el-button type="primary" @click="saveModification" :disabled="!canSave">Сохранить</el-button>
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
import {computed, defineProps, inject, onMounted, reactive, ref} from "vue";
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

function loadImages() {
    const ids = tableData.value
        .map((row) => row.primary_product_id)
        .filter(Boolean)

    if (ids.length === 0) return

    axios.get(route('admin.photo.get-by-ids'), {
        params: {
            imageableIds: ids,
            modelType: 'catalog.product',
            type: 'gallery',
        }
    }).then(response => {
        tableData.value = tableData.value.map(row => ({
            ...row,
            image: response.data[row.primary_product_id] || null,
        }))
    })
}

onMounted(loadImages)

interface ListItem {
    id: Number
    name: String,
}
const placeholder_name = ref(null)
const placeholder_attr = ref(null)
const attributes = ref<ListItem[]>([])
const form = reactive({
    productId: null,
    name: null,
    attributes: [],
})

function onOpenDialog() {
    form.productId = null
    form.name = null
    form.attributes = []
    attributes.value = []
    placeholder_name.value = null
    placeholder_attr.value = null
    dialogCreate.value = true
}

function onProductId(val) {
    form.productId = val
}

function onProductName(name) {
    form.name = name
}

function onProductAttributes(list) {
    attributes.value = list
    form.attributes = list.map(item => item.id)
}

const canSave = computed(() =>
    form.productId !== null &&
    form.attributes.length > 0 &&
    form.attributes.length <= 3
)

function saveModification() {
    router.post(route('admin.catalog.modification.store', form))
}

function routeClick(row) {
    router.get(route('admin.catalog.modification.show', {id: row.id}))
}

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.catalog.modification.destroy', {id: row.id}));
}
</script>
