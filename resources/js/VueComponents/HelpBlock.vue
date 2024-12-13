<template>
    <div class="bg-orange-100 border border-orange-500 rounded-md relative p-3 help-block">
        <i class="fa-light fa-lightbulb-on text-orange-500 mt-5 mr-3"></i>
        <h2 class="text-lg font-medium">
            Информация
        </h2>
        <div class="mt-5 font-medium"></div>
        <div class=" mt-2 text-slate-600">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">

</script>
<style lang="scss">
.help-block {
    i {
        position: absolute;
        font-size: 40px;
        right: 0;
        top: 0;
    }
    p {
        margin: 10px 0;
    }
}
</style>
