<template>
    <Head><title>{{ title }}</title></Head>
    <el-config-provider :locale="ru">
        <h1 class="font-medium text-xl">Фид {{ feed.name }}</h1>
        <div class="p-5 bg-white rounded-md">
            <FeedInfo :feed="feed" />
        </div>

        <el-splitter layout="vertical">
            <el-splitter-panel v-model:size="size" :min="50">
                <el-row>
                        <el-col :span="12" class="bg-green-50">
                            <div class="flex bg-white rounded-lg p-2">
                            <SearchAddProduct
                                :route="route('admin.unload.feed.add-product', {feed: feed.id, in: true})"
                                caption="Включить"
                                button="success"
                            />
                            <SearchAddProducts :route="route('admin.unload.feed.add-products', {feed: feed.id, in: true})" class="ml-3"/>

                            <el-tooltip content="Очистить все товары" effect="dark" placement="top-start">
                                <el-button type="danger" plain @click="clearProducts" class="ml-1"><i
                                    class="fa-light fa-trash"></i></el-button>
                            </el-tooltip>
                            </div>
                            <el-tag
                                v-for="product in Feed.products_in"
                                type="success"
                                effect="plain"
                                closable
                                @close="delProduct(product.id, true)"
                            >{{ product.code }}
                            </el-tag>
                        </el-col>
                        <el-col :span="12" class="bg-red-50">
                            <div class="flex bg-white rounded-lg p-2">
                                <SearchAddProduct
                                    :route="route('admin.unload.feed.add-product', {feed: feed.id, in: false})"
                                    caption="Исключить"
                                    button="danger"
                                />
                                <SearchAddProducts :route="route('admin.unload.feed.add-products', {feed: feed.id, in: false})" class="ml-3"/>

                                <el-tooltip content="Очистить все товары" effect="dark" placement="top-start">
                                    <el-button type="danger" plain @click="clearProducts" class="ml-1"><i
                                        class="fa-light fa-trash"></i></el-button>
                                </el-tooltip>
                            </div>
                            <el-tag
                                v-for="product in Feed.products_out"
                                type="danger"
                                effect="plain"
                                closable
                                @close="delProduct(product.id, false)"
                            >{{ product.code }}
                            </el-tag>
                        </el-col>
                    </el-row>
            </el-splitter-panel>
            <el-splitter-panel v-model:size="size2" :min="50">
                <el-row class="bg-white rounded-lg p-2 w-[100%]">
                    <div class="flex">

                        <el-select v-model="tag_id" clearable filterable>
                            <el-option v-for="tag in tags" :label="tag.name" :value="tag.id"/>
                        </el-select>
                        <el-checkbox v-model="tag_in" :checked="true" class="ml-2">Включать</el-checkbox>
                        <el-button type="primary" class="ml-3" @click="addTag">
                            <i class="fa-light fa-tags mr-2"></i>
                            Добавить
                        </el-button>
                    </div>
                </el-row>
                <el-row>
                    <el-col :span="12" class="bg-green-50">
                        <el-tag
                            v-for="tag in Feed.tags_in"
                            type="success"
                            effect="plain"
                            closable
                            @close="delTag(tag.id)"
                        >{{ tag.name }}
                        </el-tag>
                    </el-col>
                    <el-col :span="12" class="bg-red-50">
                        <el-tag
                            v-for="tag in Feed.tags_out"
                            type="danger"
                            effect="plain"
                            closable
                            @close="delTag(tag.id)"
                        >{{ tag.name }}
                        </el-tag>
                    </el-col>
                </el-row>
            </el-splitter-panel>
            <el-splitter-panel>
                <el-row>
                    <div class="flex bg-white rounded-lg p-2 w-[100%] font-bold">
                        Категории
                    </div>
                </el-row>
                <el-row>
                    <el-col :span="12">
                        <el-tree
                            class="!bg-green-50"
                            style="max-width: 600px"
                            :data="categories"
                            show-checkbox
                            node-key="id"
                            :props="defaultProps"
                            :default-checked-keys="[...Feed.categories_in]"
                            @check="onCheckIn"
                        />
                    </el-col>
                    <el-col :span="12">
                        <el-tree
                            class="!bg-red-50"
                            style="max-width: 600px"
                            :data="categories"
                            show-checkbox
                            node-key="id"
                            :props="defaultProps"
                            :default-checked-keys="[...Feed.categories_out]"
                            @check="onCheckOut"
                        />
                    </el-col>
                </el-row>
            </el-splitter-panel>
        </el-splitter>
    </el-config-provider>
</template>

<script setup lang="ts">
import {defineProps, ref} from "vue";
import ru from 'element-plus/dist/locale/ru.mjs'
import {Head, router} from "@inertiajs/vue3";
import SearchAddProducts from "@Comp/Search/AddProducts.vue";
import SearchAddProduct from "@Comp/Search/AddProduct.vue";
import FeedInfo from "./Block/Info.vue"

const props = defineProps({
    feed: Object,
    tags: Array,
    categories: Array,
    title: {
        type: String,
        default: 'Карточка фида',
    },
})
const defaultProps = {
    children: 'children',
    label: 'name',
}

const Feed = ref(props.feed)

const tag_id = ref(null)
const tag_in = ref(true)
const size = ref(100)
const size2 = ref(100)

function onCheckIn(item, data) {
    saveData('categories', {categories: data.checkedKeys, in: true})
}

function onCheckOut(item, data) {
    saveData('categories', {categories: data.checkedKeys, in: false})
}

function addTag() {
    saveData('add-tag', {tag_id: tag_id.value, tag_in: tag_in.value})
}

function delProduct(id, _in) {
    saveData('del-product', {product_id: id, in: _in})
}

function delTag(id) {
    saveData('del-tag', {tag_id: id})
}

function clearProducts() {
    saveData('del-products', {})
}

function saveData(_action, _data) {
    router.visit(route('admin.unload.feed.' + _action, {feed: props.feed.id}), {
        only: ['feed', 'flash'],
        method: "post",
        data: _data,
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            Feed.value = page.props.feed
        }
    })
}
</script>


