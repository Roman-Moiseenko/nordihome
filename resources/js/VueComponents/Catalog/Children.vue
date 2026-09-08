<template>
    <div ref="listRef" class="catalog-children">
        <CatalogRow
            v-for="item in children"
            :key="item.id"
            :item="item"
            :resource="resource"
        />
    </div>
</template>

<script setup lang="ts">
import {computed, onBeforeUnmount, onMounted, ref} from "vue";
import Sortable from "sortablejs";
import {router} from "@inertiajs/vue3";
import CatalogRow from "@Comp/Catalog/Row.vue";

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
    items: {
        type: Object,
        default: null,
    },
    resource: {
        type: String,
        default: 'category',
    },
})

const children = computed(() => {
    return props.item === null ? props.items : props.item.children
})

const listRef = ref<HTMLElement | null>(null)
let sortable: Sortable | null = null

function setSortingState(source: HTMLElement | null) {
    document.body.classList.add('catalog-is-sorting')
    if (source) {
        source.classList.add('catalog-is-source')
    }
}

function clearSortingState() {
    document.body.classList.remove('catalog-is-sorting')
    document.querySelectorAll('.catalog-children.catalog-is-source').forEach(el => {
        el.classList.remove('catalog-is-source')
    })
}

function onStart(evt: Sortable.SortableEvent) {
    setSortingState(evt.from as HTMLElement)
}

function onMove(evt: Sortable.SortableEvent): boolean {
    // Сортировка разрешена только внутри одного списка (одного родителя).
    return evt.to === evt.from
}

function onEnd(evt: Sortable.SortableEvent) {
    clearSortingState()

    const oldIndex = evt.oldIndex
    const newIndex = evt.newIndex

    if (oldIndex === undefined || newIndex === undefined || oldIndex === newIndex) {
        return
    }

    const list = children.value
    const moved = Array.isArray(list) ? list[oldIndex] : null
    if (!moved || moved.id === undefined) {
        return
    }

    router.visit(route(`admin.catalog.${props.resource}.move`, {id: moved.id}), {
        method: "post",
        data: {position: newIndex},
        preserveScroll: true,
        preserveState: false,
        onError: () => {
            // При ошибке возвращаем порядок, перезагрузив страницу с серверными данными.
            router.reload({preserveScroll: true, preserveState: false})
        },
    })
}

onMounted(() => {
    if (!listRef.value) {
        return
    }

    sortable = Sortable.create(listRef.value, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'catalog-sortable-ghost',
        chosenClass: 'catalog-sortable-chosen',
        dragClass: 'catalog-sortable-drag',
        onStart,
        onMove,
        onEnd,
    })
})

onBeforeUnmount(() => {
    clearSortingState()
    if (sortable) {
        sortable.destroy()
        sortable = null
    }
})
</script>

<style>
.catalog-children {
    min-height: 2px;
}

.drag-handle {
    cursor: grab;
    color: #94a3b8;
    user-select: none;
}

.drag-handle:active {
    cursor: grabbing;
}

.catalog-sortable-ghost {
    opacity: 0.4;
    background: #c8ebfb;
}

.catalog-sortable-chosen {
    cursor: grabbing;
}

/* Пока идёт перетаскивание, над «чужими» списками курсор показывает запрет. */
body.catalog-is-sorting .catalog-children:not(.catalog-is-source) {
    cursor: not-allowed;
}

body.catalog-is-sorting .catalog-children.catalog-is-source {
    cursor: grabbing;
}
</style>
