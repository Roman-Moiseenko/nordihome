<template>
    <div>


        <el-row :gutter="24" class="mt-3">
            <!-- Колонка 1 -->
            <el-col :span="9">
                <el-form label-position="left">
                    <el-form-item label="Название">
                        <el-input v-model="info.name"/>
                    </el-form-item>
                    <div class="flex">
                        <el-form-item label="Цена базовая">
                            <el-input-number v-model="info.basePrice" :min="0" :controls="false" style="width: 100%;">
                                <template #suffix>₽</template>
                            </el-input-number>
                        </el-form-item>
                        <el-form-item label="Упаковка" class="ml-3">
                            <el-input-number v-model="pricePackageModel" :min="0" :controls="false"
                                             style="width: 100%;">
                                <template #suffix>₽</template>
                            </el-input-number>
                        </el-form-item>
                    </div>
                    <el-form-item label="Цена объявления">
                        <el-input-number
                            v-model="info.sellPrice"
                            :min="0"
                            :controls="false"
                            :placeholder="String(sellPricePlaceholder)"
                            style="width: 100%;"
                        >
                            <template #suffix>₽</template>
                        </el-input-number>
                    </el-form-item>
                    <el-form-item label="Страна происхождения">
                        <el-input v-model="info.country"/>
                    </el-form-item>
                </el-form>
            </el-col>

            <!-- Колонка 2 -->
            <el-col :span="9">
                <el-form label-position="left">
                    <el-form-item label="ТН ВЭД">
                        <el-input v-model="info.tnVed"/>
                    </el-form-item>
                    <el-form-item label="ОКПД2">
                        <el-input v-model="info.okpd2"/>
                    </el-form-item>
                    <el-form-item label="Категория">
                        <div class="flex items-center w-full gap-2">
                            <div class="flex-1 text-gray-700">
                                {{ product.categoryPath || 'Категория не выбрана' }}
                            </div>
                            <el-button @click="openCategoryDialog">Изменить</el-button>
                        </div>
                    </el-form-item>

                    <el-form-item label="Товар каталога">
                        <div v-if="product.productCode" class="flex items-center w-full">
                            <span class="font-medium">{{ product.productCode }}</span>
                            <el-button class="ml-3" type="danger" plain @click="detachDialog = true">
                                Удалить связанный товар
                            </el-button>
                        </div>
                        <SearchAddProduct
                            v-else
                            :route="route('admin.marketplace.avito.product.attach-product', { id: product.id })"
                            caption="Привязать товар"
                        />
                    </el-form-item>
                </el-form>
            </el-col>

            <!-- Колонка 3 -->
            <el-col :span="6">
                <el-form label-position="left">
                    <el-form-item label="Авитодоставка">
                        <el-switch v-model="info.delivery"/>
                    </el-form-item>
                    <el-form-item label="Доступность">
                        <el-switch v-model="info.inStock"/>
                    </el-form-item>
                    <el-form-item label="Публиковать">
                        <el-switch v-model="info.active"/>
                    </el-form-item>
                    <el-form-item label="Распродажа">
                        <el-switch v-model="info.sale"/>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <div class="flex items-center">
            <div class="flex items-center gap-2">
                <template v-if="hasChanges">
                    <el-button type="primary" @click="$emit('save')">Сохранить</el-button>
                    <el-button @click="$emit('cancel')">Отмена</el-button>
                </template>
            </div>
        </div>
        <!-- Диалог выбора категории (дерево, как на странице Категории Авито) -->
        <el-dialog v-model="categoryDialog" title="Выбор категории" width="640px">
            <el-tree
                :data="categories"
                node-key="id"
                :props="{ label: 'name', children: 'children' }"
                highlight-current
                :expand-on-click-node="false"
                @node-click="onCategoryNodeClick"
            />
            <template #footer>
                <el-button @click="categoryDialog = false">Отмена</el-button>
                <el-button type="primary" :disabled="!selectedCategoryId" @click="selectCategory">
                    Выбрать
                </el-button>
            </template>
        </el-dialog>

        <!-- Предупреждение об удалении связанного товара -->
        <el-dialog v-model="detachDialog" title="Удаление связанного товара" width="440px">
            <p>Вы действительно хотите удалить связь с товаром каталога?</p>
            <template #footer>
                <el-button @click="detachDialog = false">Отмена</el-button>
                <el-button type="danger" @click="detachProduct">Удалить</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {route} from "ziggy-js";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";

const props = defineProps({
    product: Object,
    info: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    hasChanges: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['save', 'cancel', 'category-changed', 'product-changed'])

// Расчётная цена объявления для плейсхолдера: берём нужную из двух цен,
// пришедших с бэкенда (с доставкой / без доставки) в зависимости от переключателя.
const sellPricePlaceholder = computed<number>(() => {
    return props.info.delivery
        ? (props.product.calculatedSellPriceWithDelivery ?? 0)
        : (props.product.calculatedSellPriceWithoutDelivery ?? 0)
})

// Упаковка: 0 в БД означает «не установлено» — в поле показываем пусто.
const pricePackageModel = computed<number | null>({
    get: () => (props.info.pricePackage === 0 ? null : props.info.pricePackage),
    set: (value) => {
        props.info.pricePackage = value ?? 0
    },
})

const categoryDialog = ref(false)
const selectedCategoryId = ref<number | null>(null)

function openCategoryDialog() {
    selectedCategoryId.value = null
    categoryDialog.value = true
}

function onCategoryNodeClick(data: any) {
    // Выбор доступен только для конечной категории (без дочерних).
    if (data.children && data.children.length) {
        selectedCategoryId.value = null
        return
    }
    selectedCategoryId.value = data.id
}

function selectCategory() {
    if (!selectedCategoryId.value) return

    router.post(route('admin.marketplace.avito.product.category', {id: props.product.id}), {
        avitoCategoryId: selectedCategoryId.value,
    }, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => emit('category-changed'),
    })
}

const detachDialog = ref(false)

function detachProduct() {
    router.post(route('admin.marketplace.avito.product.detach-product', {id: props.product.id}), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => emit('product-changed'),
    })
}
</script>

<style scoped>

</style>
