<template>
    <div class="elements my-auto">
        <el-popover :visible="visible" placement="bottom-start" :width="246" v-click-outside="onClickOutside">
            <el-button v-if="show_close" @click="visible = false" type="info" class="mb-2" circle>
                <el-icon><Close /></el-icon>
            </el-button>

            <el-form @keyup.esc="cancelFilter" @submit.prevent @keyup.enter="selectSend">
                <slot />
                <div class="mt-2">
                    <el-button @click="cancelFilter" native-type="button">Сбросить</el-button>
                    <el-button ref="send_filter" @click="sendFilter" native-type="button" type="primary">Фильтр</el-button>
                </div>
            </el-form>
            <template #reference>
                <el-badge :value="count" class="item" color="green">
                    <el-button @click="visible = !visible"  ref="buttonRef" type="primary">
                        <el-icon><Filter /></el-icon>
                    </el-button>
                </el-badge>
            </template>
        </el-popover>
    </div>
</template>

<script lang="ts" setup>
import { ClickOutside as vClickOutside } from 'element-plus'
import { ref } from 'vue'
import { router, usePage } from "@inertiajs/vue3";

const send_filter = ref()

const props = defineProps({
    filter: Object,
    count: Number,
    show_close: {
        type: Boolean,
        default: true,
    },
})
const visible = ref(false)
const filter = ref(props.filter)

function selectSend() {
    console.log(send_filter.value)
    send_filter.value.ref.focus()
}
function cancelFilter() {
    console.log(999)

    router.get(window.location.href.split("?")[0])
}
function onClickOutside() {
    visible.value = false
}
function sendFilter() {
    router.get(usePage().url, filter.value)
}
</script>


<style lang="scss">
.item {
    margin-right: 30px;
}
</style>
