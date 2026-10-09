<template>
    <el-tab-pane :name="name">
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-gear"></i>
                <span> Модификации</span>
            </span>
        </template>
        <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
            <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
        </div>

        <el-row v-else :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="6">
                <div v-if="!data" class="text-sm">
                    Для данного товара Модификации не заданы.
                    <el-button type="primary" size="small" class="mt-2" @click="onOpenCreate">
                        Создать модификацию из данного товара
                    </el-button>
                </div>
                <div v-else>
                    <span class="font-medium">{{ data.name }}</span>
                    <div v-for="item in data.attributes" :key="item.id" class="flex items-center mt-2">
                        <h2 class="font-medium">{{ item.name }}</h2>
                    </div>
                    <div class="mt-5 text-sm">
                        Изменить товары в модификации или удалить товар из списка можно в разделе
                        <Link type="primary"
                           :href="route('admin.catalog.modification.show', {id: data.id})">Модификации</Link>
                    </div>
                </div>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="18" v-if="data">
                <div v-for="prod in data.products" :key="prod.id" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 180px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2">
                        <Link
                            :type="prod.isPrimary ? 'danger' : 'primary'"
                              :href="route('admin.catalog.product.edit', {id: prod.productId})">
                            {{ prod.name }}
                        </Link>
                    </span>
                    <div class="ml-auto">
                        <el-tag v-for="(value, index) in prod.values" :key="index" type="primary" effect="dark" class="ml-2">{{ value }}</el-tag>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-dialog v-model="dialogCreate" title="Новая модификация" width="500">
            <el-form label-width="auto">
                <el-form-item label="Название модификации" label-position="top" class="mt-3">
                    <el-input v-model="form.name" placeholder="Название модификации"/>
                </el-form-item>
                <el-form-item label="Атрибуты модификации" label-position="top" class="mt-3">
                    <el-select v-model="form.attributes" multiple style="width: 100%;">
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
    </el-tab-pane>
</template>

<script setup lang="ts">
import {computed, reactive, ref, watch} from "vue"
import {Link, router} from "@inertiajs/vue3"
import {route} from "ziggy-js"
import {ElMessage} from "element-plus"
import api from "@Res/api"
import {useProductPanel} from "./useProductPanel"

const props = defineProps({
    productId: {
        type: Number,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    name: {
        type: String,
        default: 'modification',
    },
})

const data = ref(null)

const {load, loading, loaded, setCache} = useProductPanel(() => `modification:${props.productId}`, async () => {
    return api.get(
        route('admin.catalog.product.edit.modification', {id: props.productId}),
        null,
        {showSuccess: false},
    )
})

watch(() => [props.active, props.productId], async ([active]) => {
    if (!active) return
    const {data: responseData} = await load()
    // Принимаем только валидную модификацию (с id), иначе считаем, что её нет.
    data.value = responseData?.id ? responseData : null
}, {immediate: true})

// Создание модификации из текущего товара
const dialogCreate = ref(false)
const attributes = ref([])
const form = reactive({
    name: '',
    attributes: [],
})

const canSave = computed(() =>
    form.name.trim() !== '' &&
    form.attributes.length > 0 &&
    form.attributes.length <= 3
)

async function onOpenCreate() {
    try {
        const result = await api.get(
            route('admin.catalog.product.edit.modification-attributes', {id: props.productId}),
            null,
            {showSuccess: false},
        )
        const list = result?.attributes ?? []
        attributes.value = list
        form.attributes = list.map(item => item.id)
        form.name = result?.name ?? ''

        if (list.length === 0) {
            ElMessage.warning('Из данного товара нельзя создать модификацию')
            return
        }
        dialogCreate.value = true
    } catch (e) {
        ElMessage.error('Не удалось получить атрибуты товара')
    }
}

function saveModification() {
    router.post(route('admin.catalog.modification.store'), {
        name: form.name,
        productId: props.productId,
        attributes: form.attributes,
    })
}
</script>
