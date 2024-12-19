<template>
    <div class="elements my-auto">
        <el-popover :visible="visible" placement="bottom-start" :width="246" v-click-outside="onClickOutside">
            <el-button v-if="show_close" @click="visible = false" type="info" class="mb-2" circle>
                <el-icon><Close /></el-icon>
            </el-button>

            <el-form :model="filter">
                <slot />
                <div class="mt-2">
                    <el-button @click="cancelFilter">Сбросить</el-button><el-button @click="sendFilter" type="primary">Фильтр</el-button>
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

</script>

<script lang="ts">
import {router} from "@inertiajs/vue3";

export default {
    props: {
        filter: Object,
        count: Number,
        show_close: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            visible: false,
            filter: this.$props.filter,
        }
    },
    methods: {
        sendFilter() {
            router.get(this.$page.url, this.$data.filter)
        },
        cancelFilter() {
            router.get(window.location.href.split("?")[0])
        },
        onClickOutside() {
            this.$data.visible = false
        },
    }
}
</script>

<style lang="scss">
.item {
 //   margin-top: 10px;
    margin-right: 30px;
}
</style>
