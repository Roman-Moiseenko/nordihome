<template>
    <EditField :field="product.name" @update:field="saveNameProduct" />
</template>

<script setup lang="ts">
import {router} from '@inertiajs/vue3'
import EditField from "@Comp/Elements/EditField.vue";

const props = defineProps({
    product: Object,
})

function saveNameProduct(val) {

    //router.post(route('admin.product.rename', {product: props.product.id}), {name: val})

    router.visit(route('admin.product.rename', {product: props.product.id}), {
        method: "post",
        data: {name: val},
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

<style scoped>

</style>
