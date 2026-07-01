<template>
    <el-row :gutter="10">
        <el-col :span="4">
            <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.room" :entity-id="room.id" type="image" />
            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.room" :entity-id="room.id" type="icon" />
            </el-tooltip>
        </el-col>
        <el-col :span="9">
            <el-form label-width="auto">
                <el-form-item label="Родительская комната">
                    <el-select v-model="info.parentId">
                        <template v-for="item in useCatalog.roomsForFilters" :key="item.id">
                            <el-option  v-if="item.id !== room.id" :value="item.id" :label="item.name" />
                        </template>
                    </el-select>
                </el-form-item>
                <el-form-item label="Название категории">
                    <el-input v-model="info.name"/>
                </el-form-item>
                <el-form-item label="Ссылка">
                    <el-input v-model="info.slug" clearable/>
                </el-form-item>
                <el-form-item label="SVG">
                    <el-input v-model="info.svgIcon" clearable type="textarea" :rows="3"/>
                </el-form-item>
                <el-form-item label="Meta-Title">
                    <el-input v-model="info.title" />
                </el-form-item>
                <el-form-item label="Meta-Description">
                    <el-input v-model="info.description" type="textarea" :rows="5"/>
                </el-form-item>

                <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button v-if="hasChanges" type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="1"></el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название комнаты</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg. Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их пустыми.</p>
            </HelpBlock>
        </el-col>
    </el-row>


</template>

<script setup>
import {computed, reactive, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import HelpBlock from "@Comp/HelpBlock.vue";
import PhotoDTO from "@Comp/PhotoDTO.vue";
import {useCatalogStore} from "@Res/catalogStore.ts";


const props = defineProps({
    room: Object,
})
const useCatalog = useCatalogStore()
const iSavingInfo = ref(false)

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.room?.name ?? '',
    title: props.room?.title ?? '',
    description: props.room?.description ?? '',
    slug: props.room?.slug ?? '',
    parentId: props.room?.parentId ?? null,
    svgIcon: props.room?.svgIcon ?? '',
}

const info = reactive({...initialInfo})

// --- Отслеживание изменений ---
const hasChanges = computed(() => {
    for (const key of Object.keys(initialInfo)) {
        const a = JSON.stringify(info[key])
        const b = JSON.stringify(initialInfo[key])
        if (a !== b) return true
    }
    return false
})
function onCancel() {
    Object.assign(info, {...initialInfo})
}

function onSetInfo() {
    iSavingInfo.value = true;
    router.visit(
        route('admin.catalog.room.update', {room: props.room.id}), {
            method: "put",
            data: info,
            preserveScroll: true,
            preserveState: false,
            onSuccess: page => {
                iSavingInfo.value = false;
            },
            onError: errors => {
                iSavingInfo.value = false;
            }
        }
    );
}

</script>

<style scoped>
    span.svg-category::v-deep>svg {
        max-height: 50px;
    }
</style>
