<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Категория размеров {{ category.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            Название и переименовать
        </div>
        <div class="flex mt-5">
            Добавить размер
        </div>

        <div class="p-5 bg-white rounded-md">
            <el-table
                :data="tableData"
                header-cell-class-name="nordihome-header"
                style="width: 100%; cursor: pointer;"
            >
                <el-table-column prop="name" label="Размер">
                    <template #default="scope">
                        <EditField :field="scope.row.name" @update:field="val => saveNameSize(scope.row.id, val)" />
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
    </el-config-provider>
    <DeleteEntityModal name_entity="Размер из категорит" />
</template>
<script lang="ts" setup>
import {inject, ref, defineProps} from "vue";
import {Head, router} from '@inertiajs/vue3'
import {useStore} from "@Res/store.js"
import ru from 'element-plus/dist/locale/ru.mjs'
import SearchAddProduct from '@Comp/Search/AddProduct.vue'
import SearchAddProducts from '@Comp/Search/AddProducts.vue'
import GroupInfo from  './Block/Info.vue'
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    category: Object,
    title: {
        type: String,
        default: 'Карточка группы товаров',
    },

})
const store = useStore();
const $delete_entity = inject("$delete_entity")
const tableData = ref([...props.category.sizes])

function handleDeleteEntity(row) {
    $delete_entity.show(route('admin.product.size.del-size', {size: row.id}));
}
function saveNameSize(id, val) {

    //router.post(route('admin.product.rename', {product: props.product.id}), {name: val})

    router.visit(route('admin.product.size.set-size', {size: id}), {
        method: "post",
        data: {name: val},
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

