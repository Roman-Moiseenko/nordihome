<template>
    <div class="flex relative">
        <div class="relative" style="width: 180px;">
            <el-input v-model="data" :class="focus.class" style="width: 180px"
                      type="textarea" :rows="focus.rows" resize="none"
                      @focusin="inFocus" @focusout="outFocus"
            />
        </div>
        <el-button type="primary" plain @click="onAdd" class="ml-1">{{ caption }}</el-button>
    </div>
</template>

<script setup>
import {reactive, ref, defineProps} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    route: String,
    caption: {
        default: 'Добавить товары',
        type: String
    }
})
const data = ref(null)
const focus = reactive({
    rows: 1,
    class: 'out-focus',
})

function onAdd() {
    let products = [], string = '', item = [];
    let array = data.value.split("\n")
    for (let i in array) {
        string = array[i].replace(/ +/g, ' ')
        if (string !== '') {
            item = string.split(' ')
            products.push({
                code:item[0],
                quantity: (item[1] === undefined) ? 1 : Number(item[1]),
            })
        }
    }
    if (products.length === 0) return;
    router.visit(props.route, {
        method: "post",
        data: {products: products},
        preserveScroll: true,
        preserveState: false,
        onSuccess: page => {
            data.value = null
        }
    })
}
function inFocus() {
    focus.rows = 12
    focus.class = 'in-focus'
}
function outFocus() {
    focus.rows = 1
    focus.class = 'out-focus'
}
</script>
<style lang="scss">
.in-focus {
    position: absolute;
    z-index: 999;
    top: 0;
    width: 180px;
}
.out-focus {
    > textarea {
        overflow: hidden !important;
    }
}
</style>
