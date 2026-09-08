<template>
    <div class="bg-white rounded-md flex items-center mb-1 p-2 border border-slate-200">
        <div>
            <i v-if="!item.published" class="fa-light fa-lock"></i>
        </div>
        <div class="w-11" style="height: 40px;">
            <img v-if="item.image_url" :src="item.image_url" style="width: 40px; height: 40px;">
        </div>
        <div class="w-11 ml-2">
            <img v-if="item.icon_url" :src="item.icon_url" style="width: 40px; height: 40px;">
        </div>
        <div class="ml-4" style="width: 350px;">
            <Link type="primary" :href="showUrl">{{ item.name }}</Link>
        </div>
        <div class="ml-4" style="width: 350px;">
            <span class="text-cyan-800">{{ config.slug }}{{ item.slug }}</span>
        </div>
        <div class="ml-5 text-center" style="width: 150px;">
            <span v-if="isChildren">
                {{ item.children.length }}
                <el-button type="info" size="small" class="ml-2" plain @click="checkChildren = !checkChildren">
                    <i v-if="checkChildren" class="fa-regular fa-chevron-up"></i>
                    <i v-else class="fa-regular fa-chevron-down"></i>
                </el-button>
            </span>
        </div>
        <div class="flex ml-5">
            <el-button size="small"
                       type="primary"
                       @click.stop="onUp()"
            >
                <i class="fa-light fa-arrow-up"></i>
            </el-button>
            <el-button size="small"
                       type="primary"
                       @click.stop="onDown()"
            >
                <i class="fa-light fa-arrow-down"></i>
            </el-button>
            <el-popover :visible="visible_create" placement="bottom-start" :width="246">
                <template #reference>
                        <el-button size="small"
                                   type="success"
                                   @click="visible_create = !visible_create" ref="buttonRef"
                        >
                            <i class="fa-light fa-folder-plus"></i>
                        </el-button>
                </template>
                <el-input v-model="form.name" placeholder="Дочерняя категория"/>
                <div class="mt-2">
                    <el-button @click="visible_create = false">Отмена</el-button><el-button @click="handleChild" type="primary">Создать</el-button>
                </div>
            </el-popover>
            <el-button size="small"
                       :type="item.published ? 'warning' : 'success'"
                       @click.stop="onToggle()"
            >
                <i class="fa-light" :class="item.published ? 'fa-lock' : 'fa-lock-open'"></i>
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
        <CatalogChildren :item="item" :resource="resource" />
    </div>

</template>

<script setup lang="ts">
import {router, Link} from "@inertiajs/vue3";
import {computed, inject, reactive, ref} from "vue";
import CatalogChildren from "@Comp/Catalog/Children.vue";
import {useCatalogStore} from "@Res/catalogStore";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    resource: {
        type: String,
        default: 'category',
    },
})

const $emit = defineEmits(['delete:item'])

/**
 * Конфигурация справочника. Компоненты Category и Room идентичны
 * и отличаются только маршрутом и именем параметра в resource-маршруте.
 */
const resources = {
    category: {
        route: 'admin.catalog.category',
        slug: '/catalog/',
    },
    room: {
        route: 'admin.catalog.room',
        slug: '/room/',
    },
}

const config = computed(() => resources[props.resource] ?? resources.category)

const showUrl = computed(() => route(`${config.value.route}.show`, {id: props.item.id}))
const destroyUrl = computed(() => route(`${config.value.route}.destroy`, {id: props.item.id}))
const storeUrl = computed(() => route(`${config.value.route}.store`))

const visible_create = ref(false)
const form = reactive({
    name: null,
    parentId: props.item.id,
})
const checkChildren = ref(false)
const isChildren = ref(props.item.children.length > 0)
const $delete_entity = inject("$delete_entity")

const showChildren = computed(() => {
    return isChildren.value && checkChildren.value
})

function onUp() {
    router.visit(route(`${config.value.route}.up`, {id: props.item.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onDown() {
    router.visit(route(`${config.value.route}.down`, {id: props.item.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}
function onToggle() {
    router.visit(route(`${config.value.route}.toggle`, {id: props.item.id}), {
        method: "post",
        preserveScroll: true,
        preserveState: false,
    })
}

function handleDeleteEntity() {
    $delete_entity.show(destroyUrl.value, {name: props.resource});
}
function handleChild() {
    router.visit(storeUrl.value, {
        method: "post",
        data: form,
        onSuccess: page => {
            useCatalogStore().reload()
        }
    })
}


</script>
