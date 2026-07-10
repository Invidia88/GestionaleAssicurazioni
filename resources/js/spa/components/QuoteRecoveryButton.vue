<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { createQuoteFromPolicy } from '../lib/api.js';
import { isRecoveryQuoteAvailable } from '../lib/domain.js';

const props = defineProps({
    policy: {
        type: Object,
        required: true,
    },
    companies: {
        type: Array,
        required: true,
    },
});

const router = useRouter();
const open = ref(false);
const companyId = ref('');
const saving = ref(false);
const error = ref('');

const submit = async () => {
    if (!companyId.value) {
        error.value = 'Seleziona una compagnia.';
        return;
    }

    saving.value = true;
    error.value = '';

    try {
        await createQuoteFromPolicy(props.policy, companyId.value);
        open.value = false;
        await router.push('/quotes');
    } catch (exception) {
        error.value = exception.message;
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <button
        v-if="isRecoveryQuoteAvailable(policy)"
        type="button"
        class="inline-flex min-h-10 items-center justify-center rounded bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
        @click="open = true"
    >
        Crea preventivo
    </button>

    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 grid place-items-center bg-slate-950/50 px-4">
            <section class="w-full max-w-md rounded border border-slate-200 bg-white p-5 shadow-2xl">
                <h3 class="text-lg font-semibold text-slate-950">Crea preventivo recupero</h3>
                <p class="mt-1 text-sm text-slate-500">Scegli la compagnia da usare per proporre una nuova offerta al cliente.</p>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-700">Compagnia assicurativa</label>
                    <select v-model="companyId" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleziona compagnia</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>

                <p v-if="error" class="mt-3 rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ error }}</p>

                <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button type="button" class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="open = false">
                        Annulla
                    </button>
                    <button type="button" :disabled="saving" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60" @click="submit">
                        {{ saving ? 'Creazione...' : 'Continua' }}
                    </button>
                </div>
            </section>
        </div>
    </Teleport>
</template>
