<template>

    <el-row :gutter="10">
        <el-col :span="4">
            <el-tooltip content="Изображение для каталога" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.category" :entity-id="category.id" type="image" />
            </el-tooltip>
            <el-tooltip content="Иконка для меню" placement="top-start" effect="dark">
                <PhotoDTO model-type="catalog.category" :entity-id="category.id" type="icon" />
            </el-tooltip>
        </el-col>
        <el-col :span="8">
            <el-form label-width="auto">
                <el-form-item label="Родительская категория">
                    <el-select v-model="info.parentId" >
                        <template v-for="item in useCatalog.categoriesForFilters" :key="item.id">
                            <el-option  v-if="item.id !== category.id" :value="item.id" :label="item.name" />
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
                    <el-input v-model="info.metaTitle" />
                </el-form-item>
                <el-form-item label="Meta-Description">
                    <el-input v-model="info.metaDescription" type="textarea" :rows="5"/>
                </el-form-item>

                <el-button v-if="hasChanges" type="info" @click="onCancel" style="margin-left: 4px">
                    Отмена
                </el-button>
                <el-button v-if="hasChanges" type="success" @click="onSetInfo">
                    Сохранить
                </el-button>
            </el-form>
        </el-col>
        <el-col :span="1">
        </el-col>
        <el-col :span="8">
            <HelpBlock>
                <p><b>Название категории</b> является обязательным полем.</p>
                <p>Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</p>
                <p>Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</p>
                <p><b>Иконки</b> для меню рекомендуется сохранять в форматах разрешающие прозрачный цвет - png, svg. Разрешение не более 200х200.</p>
                <p>Поля <b>Meta</b> используются в SEO. Для того, чтоб они заполнялись автоматически, оставьте их пустыми.</p>
            </HelpBlock>
        </el-col>
    </el-row>

</template>

<script setup>
import {computed, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import HelpBlock from "@Comp/HelpBlock.vue";
import {useCatalogStore} from "@Res/catalogStore.ts";
import PhotoDTO from "@Comp/PhotoDTO.vue";


const props = defineProps({
    category: Object,
})
const useCatalog = useCatalogStore()
const iSavingInfo = ref(false)

// --- Исходные данные из пропсов (эталон для отмены) ---
const initialInfo = {
    name: props.category?.name ?? '',
    metaTitle: props.category?.meta?.title ?? '',
    metaDescription: props.category?.meta?.description ?? '',
    slug: props.category?.slug ?? '',
    parentId: props.category?.parent_id ?? null,
    svgIcon: props.category?.svg ?? '',
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
        route('admin.catalog.category.update', {id: props.category.id}), {
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
