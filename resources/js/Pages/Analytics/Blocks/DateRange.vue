<template>
    <div class="flex items-center gap-3">
        <el-date-picker
            v-model="range"
            type="daterange"
            value-format="YYYY-MM-DD"
            range-separator="—"
            start-placeholder="Начало"
            end-placeholder="Конец"
            :clearable="false"
            @change="apply"
        />
    </div>
</template>

<script lang="ts" setup>
import {ref} from 'vue'
import {router} from '@inertiajs/vue3'

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
})

const from = props.filters.dateFrom ?? new Date(Date.now() - 30 * 86400000).toISOString().slice(0, 10)
const to = props.filters.dateTo ?? new Date().toISOString().slice(0, 10)

const range = ref<[string, string]>([from, to])

function apply() {
    router.get(route(props.routeName), {
        dateFrom: range.value[0],
        dateTo: range.value[1],
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>
