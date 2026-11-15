import { ref } from "vue";
import axios from "axios";
import { route } from "ziggy-js";

interface AvitoCategoryField {
    id: number;
    tag: string;
    name: string;
    type: string | null;
    is_required: boolean;
    allowed_values: string[];
}

export function useAvitoCategoryFields() {
    const fields = ref<AvitoCategoryField[]>([]);
    const loading = ref(false);
    const categoryId = ref<number | null>(null);

    async function load(id: number) {
        loading.value = true;
        categoryId.value = id;
        try {
            const { data } = await axios.get(
                route("admin.marketplace.avito.category.fields", { id })
            );
            fields.value = data;
        } finally {
            loading.value = false;
        }
    }

    async function saveType(fieldId: number, type: string | null) {
        const { data } = await axios.post(
            route("admin.marketplace.avito.category.field.type", {
                id: categoryId.value,
                fieldId,
            }),
            { type }
        );

        const field = fields.value.find((f) => f.id === fieldId);
        if (field) {
            field.type = data.type;
        }
    }

    async function saveVariants(fieldId: number, variants: string[]) {
        const { data } = await axios.post(
            route("admin.marketplace.avito.category.field.variants", {
                id: categoryId.value,
                fieldId,
            }),
            { variants }
        );

        const field = fields.value.find((f) => f.id === fieldId);
        if (field) {
            field.allowed_values = data.allowed_values;
        }
    }

    return { fields, loading, categoryId, load, saveType, saveVariants };
}
