<template>
    <Card title="Товары">
        <el-row>
            <el-col :span="12" class="bg-green-50 p-2">
                <div class="flex bg-white rounded-lg p-2">
                    <SearchAddProduct
                        :route="route('admin.output.feed.update', {feed: feed.id})"
                        :params="{field: 'products', action: 'add', in: true}"
                        caption="Включить"
                        button="success"
                        preserve-state
                    />
                    <SearchAddProducts
                        :route="route('admin.output.feed.update', {feed: feed.id})"
                        :params="{field: 'products', action: 'add', in: true}"
                        preserve-state
                        class="ml-3"
                    />

                    <el-tooltip content="Очистить все товары" effect="dark" placement="top-start">
                        <el-button type="danger" plain @click="clear(true)" class="ml-1">
                            <i class="fa-light fa-trash"></i>
                        </el-button>
                    </el-tooltip>
                </div>
                <el-tag
                    v-for="product in feed.productsIn"
                    :key="product.id"
                    type="success"
                    effect="plain"
                    closable
                    @close="remove(product.id, true)"
                >{{ product.code }}</el-tag>
            </el-col>
            <el-col :span="12" class="bg-red-50 p-2">
                <div class="flex bg-white rounded-lg p-2">
                    <SearchAddProduct
                        :route="route('admin.output.feed.update', {feed: feed.id})"
                        :params="{field: 'products', action: 'add', in: false}"
                        caption="Исключить"
                        button="danger"
                        preserve-state
                    />
                    <SearchAddProducts
                        :route="route('admin.output.feed.update', {feed: feed.id})"
                        :params="{field: 'products', action: 'add', in: false}"
                        preserve-state
                        class="ml-3"
                    />

                    <el-tooltip content="Очистить все товары" effect="dark" placement="top-start">
                        <el-button type="danger" plain @click="clear(false)" class="ml-1">
                            <i class="fa-light fa-trash"></i>
                        </el-button>
                    </el-tooltip>
                </div>
                <el-tag
                    v-for="product in feed.productsOut"
                    :key="product.id"
                    type="danger"
                    effect="plain"
                    closable
                    @close="remove(product.id, false)"
                >{{ product.code }}</el-tag>
            </el-col>
        </el-row>
    </Card>
</template>

<script setup lang="ts">
import {defineProps} from "vue";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import Card from "./Card.vue";

const props = defineProps({
    feed: {type: Object, required: true},
    save: {type: Function, required: true},
})

function remove(id: number, _in: boolean) {
    props.save('products', 'remove', _in, [id])
}

function clear(_in: boolean) {
    props.save('products', 'clear', _in, [])
}
</script>
