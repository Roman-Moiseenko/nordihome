<template>
    <el-dialog v-model="_show" title="Удалить запись" width="400" center>
        <div class="font-medium text-md mt-2">
            Вы уверены, что хотите удалить {{ name_entity }}?
        </div>
        <div class="text-red-600 text-md mt-2">
            Восстановить данные будет невозможно!
        </div>
        <slot />
        <template #footer>
            <div class="dialog-footer">
                <el-button @click="closeModal(false)">Отмена</el-button>
                <el-button type="danger" @click="closeModal(true)">
                    Удалить
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { inject, computed } from "vue"

const
    $props = defineProps({
        name: { type: String, default: "entity" },
        name_entity: { type: String, default: "Сущность" },
    }),
    $delete_entity = inject("$delete_entity"),
    _show = computed(() => {
        return $delete_entity.active() == $props.name
    })
function closeModal(accept = false) {
    accept ? $delete_entity.accept() : $delete_entity.cancel()
}

</script>

<style scoped>

</style>
