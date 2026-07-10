<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

const open = ref(false);
const selectedCompanyId = ref('');

const canCreateQuote = computed(() => Boolean(props.policy.can_create_quote));

const show = () => {
    selectedCompanyId.value = '';
    open.value = true;
};

const close = () => {
    open.value = false;
};

const goToCompany = () => {
    if (!selectedCompanyId.value) {
        return;
    }

    router.get(route('insurance-companies.show', selectedCompanyId.value), {
        quote_policy_id: props.policy.id,
    });
};
</script>

<template>
    <button
        v-if="canCreateQuote"
        type="button"
        @click="show"
        class="inline-flex min-h-10 items-center justify-center rounded border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        Crea preventivo
    </button>

    <Modal :show="open" max-width="lg" @close="close">
        <div class="p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Recupero cliente</p>
                <h2 class="mt-1 text-lg font-semibold text-slate-950">Crea preventivo</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Scegli la compagnia assicurativa da usare per proporre una nuova offerta a
                    <span class="font-semibold text-slate-900">{{ policy.client.full_name }}</span>.
                </p>
            </div>

            <div class="mt-5 rounded border border-slate-200 bg-slate-50 p-4 text-sm">
                <p class="font-semibold text-slate-950">{{ policy.number }} - {{ policy.type }}</p>
                <p class="mt-1 text-slate-600">Compagnia precedente: {{ policy.insurance_company.name }}</p>
                <p class="text-slate-600">Data scadenza: {{ new Intl.DateTimeFormat('it-IT').format(new Date(policy.end_date)) }}</p>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-slate-700">Compagnia per il preventivo</label>
                <select
                    v-model="selectedCompanyId"
                    class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Seleziona compagnia</option>
                    <option v-for="company in companies" :key="company.id" :value="company.id">
                        {{ company.name }}
                    </option>
                </select>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <SecondaryButton type="button" @click="close">Annulla</SecondaryButton>
                <PrimaryButton type="button" :disabled="!selectedCompanyId" @click="goToCompany">
                    Vai alla compagnia
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
