<template>
    <div class="bg-white rounded-md flex items-center mb-1 p-2 border border-slate-200">
        <div style="width: 100px;">
            <span>{{ category.brand_name }}</span>
        </div>
        <div>
            <Active :active="category.active"/>
        </div>
        <div class="ml-4" style="width: 300px;">
            <Link type="primary" :href="route('admin.parser.category.show', {category_parser: category.id})">{{
                    category.name
                }}
            </Link>
        </div>

        <div class="ml-4" style="width: 350px;">
            <span class="text-cyan-800">{{ category.url }}</span>
        </div>

        <div class="ml-5 text-center" style="width: 150px;">
            <span v-if="isChildren">
                {{ category.children.length }}
                <el-button type="info" size="small" class="ml-2" plain @click="checkChildren = !checkChildren">
                    <i v-if="checkChildren" class="fa-regular fa-chevron-up"></i>
                    <i v-else class="fa-regular fa-chevron-down"></i>
                </el-button>
            </span>
        </div>
        <div class="flex ml-auto">
            <el-button v-if="category.active" size="small"
                       type="warning"
                       @click.stop="onToggle()"
            >
                No Parse
            </el-button>
            <el-button v-if="!category.active" size="small"
                       type="primary"
                       @click.stop="onToggle()"
            >
                To Parse
            </el-button>

            <el-button size="small"
                       type="danger"
                       @click.stop="handleDeleteEntity"
            >
                Delete
            </el-button>
        </div>
    </div>
    <div v-if="showChildren" class="pl-5 ml-2 mb-5 pb-2 pt-2">
        <!--CategoryRow v-for="item in category.children" :category="item" @delete:category="handleDeleteEntity" /-->
        <CategoryChildren :category="category" @delete:category="handleDeleteEntity"
                          :product_categories="product_categories"/>
    </div>
</template>

<script setup lang="ts">
import {router, Link} from "@inertiajs/vue3";
import {computed, inject, onActivated, reactive, ref} from "vue";
import CategoryChildren from "./Children.vue";
import Active from "@Comp/Elements/Active.vue";

const props = defineProps({
    category: Object,
    product_categories: Array,
})
const $emit = defineEmits(['delete:category'])
const visible_create = ref(false)
const form = reactive({
    name: null,
    parent_id: props.category.id,
})
const checkChildren = ref(false)
const isChildren = ref(props.category.children.length > 0)

const showChildren = computed(() => {
    return isChildren && checkChildren.value
})
const category_id = ref(null)

function onToggle() {
    router.visit(route('admin.parser.category.toggle', {category_parser: props.category.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        onSuccess: page => {
            console.log(page.props)
        }
    })
}

function handleDeleteEntity() {
    $emit('delete:category', props.category.id)
}



</script>
