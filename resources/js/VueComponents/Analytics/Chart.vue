<template>
    <div class="analytics-chart" :style="{ height: height + 'px' }">
        <canvas :ref="el => canvas = el" />
    </div>
</template>

<script lang="ts" setup>
import {onBeforeUnmount, onMounted, ref, watch} from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
    type: {
        type: String,
        default: 'line',
    },
    labels: {
        type: Array,
        default: () => [],
    },
    datasets: {
        type: Array,
        default: () => [],
    },
    height: {
        type: Number,
        default: 300,
    },
})

const canvas = ref<HTMLCanvasElement | null>(null)
let chart: Chart | null = null

function render() {
    if (chart) {
        chart.data.labels = [...props.labels]
        chart.data.datasets = [...props.datasets]
        chart.update()
        return
    }

    if (!canvas.value) return

    chart = new Chart(canvas.value, {
        type: props.type as any,
        data: {
            labels: [...props.labels],
            datasets: [...props.datasets],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: props.type === 'pie' || props.type === 'doughnut' || props.datasets.length > 1,
                },
            },
        },
    })
}

watch(() => [props.labels, props.datasets], render, {deep: true})

onMounted(render)

onBeforeUnmount(() => {
    if (chart) {
        chart.destroy()
        chart = null
    }
})
</script>

<style scoped>
.analytics-chart {
    position: relative;
    width: 100%;
}
</style>
