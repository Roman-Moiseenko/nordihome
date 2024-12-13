<template>
    <el-tab-pane>
        <template #label>
            <span class="custom-tabs-label">
                <i class="fa-light fa-folder-gear"></i>
                <span> Модификации</span>
            </span>
        </template>
        <el-row :gutter="10" class="mt-2">
            <!-- Колонка 1 -->
            <el-col :span="6">
                <div v-if="!product.modification" class=" text-sm">
                    Для данного товара Модификации не заданы.<br>Создать группу модификаций можно в разделе
                    <Link type="primary"
                       :href="route('admin.product.modification.index')">Модификации</Link>
                </div>
                <div v-else>
                    Модификация <span class="font-medium">{{ product.modification.name }}</span>
                    <div v-for="item in product.modification.attributes" class="flex items-center mt-2">
                        <img v-if="item.image" :src="item.image" width="40" height="40"/> <h2 class="font-medium ml-2">{{ item.name }}</h2>
                    </div>
                    <div class="mt-5 text-sm">
                        Изменить товары в модификации или удалить товар из списка можно в разделе

                        <Link type="primary"
                           :href="route('admin.product.modification.show', product.modification.id)">Модификации</Link>
                    </div>
                </div>
            </el-col>
            <!-- Колонка 2 -->
            <el-col :span="12" v-if="product.modification">
                <div v-for="prod in product.modification.products" class="mt-3 flex items-center p-2 bg-slate-100 rounded-md" >
                    <img v-if="prod.image" :src="prod.image" width="40" height="40"/>
                    <span class="font-medium ml-3" style="width: 120px;">{{ prod.code }}</span>
                    <span class="font-medium ml-2"><Link type="primary" :href="route('admin.product.edit', prod.id)">{{ prod.name }}</Link></span>
                    <div class="ml-auto">
                        <el-tag v-for="attr in prod.attributes" type="primary" effect="dark" class="ml-2">{{ attr.name }}</el-tag>
                    </div>
                </div>
            </el-col>
        </el-row>
    </el-tab-pane>
</template>

<script setup lang="ts">
import { defineProps } from "vue"
import {Link} from "@inertiajs/vue3"

const props = defineProps({
    product: Object,
    errors: Object,
})

</script>
