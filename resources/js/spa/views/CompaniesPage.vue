<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import FormCard from '@/Components/FormCard.vue';
import { deleteRecord, loadCompanies, saveRecord } from '../lib/api.js';

const companies = ref([]);
const search = ref('');
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const editingId = ref(null);
const showForm = ref(false);

const form = reactive({
    name: '',
    contact_name: '',
    contact_phone: '',
    contact_email: '',
    notes: '',
});

const resetForm = () => {
    editingId.value = null;
    showForm.value = false;
    Object.assign(form, {
        name: '',
        contact_name: '',
        contact_phone: '',
        contact_email: '',
        notes: '',
    });
};

const openCreateForm = () => {
    resetForm();
    showForm.value = true;
};

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        companies.value = await loadCompanies();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const filteredCompanies = computed(() => {
    const term = search.value.toLowerCase().trim();

    if (!term) {
        return companies.value;
    }

    return companies.value.filter((company) =>
        [company.name, company.contact_name, company.contact_phone, company.contact_email]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

const edit = (company) => {
    editingId.value = company.id;
    showForm.value = true;
    Object.assign(form, {
        name: company.name ?? '',
        contact_name: company.contact_name ?? '',
        contact_phone: company.contact_phone ?? '',
        contact_email: company.contact_email ?? '',
        notes: company.notes ?? '',
    });
};

const submit = async () => {
    saving.value = true;
    error.value = '';

    try {
        await saveRecord('insurance_companies', form, editingId.value);
        await load();
        resetForm();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        saving.value = false;
    }
};

const remove = async (company) => {
    if (!confirm(`Eliminare ${company.name}? Verranno eliminate anche le polizze collegate.`)) {
        return;
    }

    try {
        await deleteRecord('insurance_companies', company.id);
        await load();
    } catch (exception) {
        error.value = exception.message;
    }
};
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-blue-700">Partner</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Compagnie</h2>
                <p class="mt-1 text-sm text-slate-500">Compagnie disponibili per polizze e preventivi.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <input v-model="search" type="search" placeholder="Cerca compagnia" class="min-h-10 rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" @click="openCreateForm">
                    Crea nuova compagnia
                </button>
            </div>
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>

        <FormCard v-if="showForm" :title="editingId ? 'Modifica compagnia' : 'Nuova compagnia'" description="Il nome compagnia e obbligatorio.">
            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                <div>
                    <label class="text-sm font-medium text-slate-700">Nome compagnia</label>
                    <input v-model="form.name" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Referente</label>
                    <input v-model="form.contact_name" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Telefono</label>
                    <input v-model="form.contact_phone" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Email</label>
                    <input v-model="form.contact_email" type="email" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row md:col-span-2">
                    <button type="submit" :disabled="saving" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
                        {{ saving ? 'Salvataggio...' : editingId ? 'Salva modifiche' : 'Crea compagnia' }}
                    </button>
                    <button type="button" class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="resetForm">
                        Annulla
                    </button>
                </div>
            </form>
        </FormCard>

        <DataTableWrapper class="hidden lg:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Referente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Contatti</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="company in filteredCompanies" :key="company.id">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-950">{{ company.name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ company.contact_name || '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            <p>{{ company.contact_phone || '-' }}</p>
                            <p>{{ company.contact_email || '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button class="rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="edit(company)">Modifica</button>
                            <button class="ml-2 rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" @click="remove(company)">Elimina</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTableWrapper>

        <div class="space-y-3 lg:hidden">
            <article v-for="company in filteredCompanies" :key="company.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <p class="font-semibold text-slate-950">{{ company.name }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ company.contact_name || 'Referente non indicato' }}</p>
                <p class="text-sm text-slate-500">{{ company.contact_phone || company.contact_email || 'Contatti non indicati' }}</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <button class="min-h-10 rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700" @click="edit(company)">Modifica</button>
                    <button class="min-h-10 rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700" @click="remove(company)">Elimina</button>
                </div>
            </article>
        </div>

        <EmptyState v-if="!loading && filteredCompanies.length === 0" title="Nessuna compagnia" description="Crea una nuova compagnia o cambia ricerca." />
    </div>
</template>
