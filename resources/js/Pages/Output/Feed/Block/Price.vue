<template>
    <Card title="Цена">
        <div class="flex flex-wrap gap-4">
            <el-form-item label="Минимальная цена">
                <el-input-number
                    v-model="priceMin"
                    :min="0"
                    :controls="false"
                    placeholder="Не ограничено"
                    style="width: 160px"
                    @change="onSave"
                />
            </el-form-item>
            <el-form-item label="Максимальная цена">
                <el-input-number
                    v-model="priceMax"
                    :min="0"
                    :controls="false"
                    placeholder="Не ограничено"
                    style="width: 160px"
                    @change="onSave"
                />
            </el-form-item>
        </div>
    </Card>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import Card from "./Card.vue";

const props = defineProps({
    feed: {type: Object, required: true},
})

const priceMin = ref(props.feed.priceMin ?? null)
const priceMax = ref(props.feed.priceMax ?? null)

function onSave() {
    router.visit(route('admin.output.feed.update', {feed: props.feed.id}), {
        only: ['feed', 'flash'],
        method: "post",
        data: {
            priceChanged: true,
            priceMin: priceMin.value ?? null,
            priceMax: priceMax.value ?? null,
        },
        preserveScroll: true,
        preserveState: true,
    });
}
</script>
