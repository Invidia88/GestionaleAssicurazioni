<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import FormCard from '@/Components/FormCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import QuoteRecoveryButton from '../components/QuoteRecoveryButton.vue';
import { deleteRecord, loadClients, loadCompanies, loadPolicies, saveRecord } from '../lib/api.js';
import { emptyPolicyForm, formatCurrency, formatDate, fullName, policyStatuses, policyTypes, statusLabel } from '../lib/domain.js';

const clients = ref([]);
const companies = ref([]);
const policies = ref([]);
const search = ref('');
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const editingId = ref(null);
const form = reactive(emptyPolicyForm());

const resetForm = () => {
    editingId.value = null;
    Object.assign(form, emptyPolicyForm());
};

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const [clientRows, companyRows, policyRows] = await Promise.all([
            loadClients(),
            loadCompanies(),
            loadPolicies(),
        ]);
        clients.value = clientRows;
        companies.value = companyRows;
        policies.value = policyRows;
    } catch (exception) {
        error.value = exception.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const filteredPolicies = computed(() => {
    const term = search.value.toLowerCase().trim();

    if (!term) {
        return policies.value;
    }

    return policies.value.filter((policy) =>
        [policy.number, policy.type, policy.status, fullName(policy.client), policy.insurance_company?.name]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

const edit = (policy) => {
    editingId.value = policy.id;
    Object.assign(form, {
        client_id: policy.client_id ?? '',
        insurance_company_id: policy.insurance_company_id ?? '',
        type: policy.type ?? 'Auto',
        number: policy.number ?? '',
        start_date: policy.start_date ?? '',
        end_date: policy.end_date ?? '',
        annual_premium: policy.annual_premium ?? '',
        status: policy.status ?? 'attiva',
        notes: policy.notes ?? '',
    });
};

const submit = async () => {
    saving.value = true;
    error.value = '';

    try {
        await saveRecord('policies', {
            ...form,
            annual_premium: Number(form.annual_premium || 0),
        }, editingId.value);
        await load();
        resetForm();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        saving.value = false;
    }
};

const remove = async (policy) => {
    if (!confirm(`Eliminare la polizza ${policy.number}?`)) {
        return;
    }

    try {
        await deleteRecord('policies', policy.id);
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
                <p class="text-xs font-semibold uppercase text-blue-700">Contratti</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Polizze</h2>
                <p class="mt-1 text-sm text-slate-500">Archivio polizze con relazioni Supabase.</p>
            </div>
            <input v-model="search" type="search" placeholder="Cerca polizza" class="min-h-10 rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>

        <FormCard :title="editingId ? 'Modifica polizza' : 'Nuova polizza'" description="Cliente, compagnia, numero e date sono obbligatori.">
            <form class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" @submit.prevent="submit">
                <div>
                    <label class="text-sm font-medium text-slate-700">Cliente</label>
                    <select v-model="form.client_id" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleziona cliente</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id">{{ fullName(client) }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Compagnia</label>
                    <select v-model="form.insurance_company_id" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleziona compagnia</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipo</label>
                    <select v-model="form.type" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option v-for="type in policyTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Numero</label>
                    <input v-model="form.number" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Decorrenza</label>
                    <input v-model="form.start_date" required type="date" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Scadenza</label>
                    <input v-model="form.end_date" required type="date" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Premio annuo</label>
                    <input v-model="form.annual_premium" type="number" min="0" step="0.01" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Stato</label>
                    <select v-model="form.status" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option v-for="status in policyStatuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </div>
                <div class="md:col-span-2 xl:col-span-3">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row md:col-span-2 xl:col-span-3">
                    <button type="submit" :disabled="saving" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
                        {{ saving ? 'Salvataggio...' : editingId ? 'Salva modifiche' : 'Crea polizza' }}
                    </button>
                    <button type="button" class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="resetForm">
                        Annulla
                    </button>
                </div>
            </form>
        </FormCard>

        <DataTableWrapper class="hidden xl:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Polizza</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Scadenza</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Premio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Stato</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="policy in filteredPolicies" :key="policy.id">
                        <td class="px-4 py-3 text-sm">
                            <p class="font-semibold text-slate-950">{{ policy.number }}</p>
                            <p class="text-slate-500">{{ policy.type }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ fullName(policy.client) }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ policy.insurance_company?.name }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-950">{{ formatDate(policy.end_date) }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ formatCurrency(policy.annual_premium) }}</td>
                        <td class="px-4 py-3"><StatusBadge :status="policy.status" :label="statusLabel(policy.status)" /></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <QuoteRecoveryButton :policy="policy" :companies="companies" />
                                <button class="rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="edit(policy)">Modifica</button>
                                <button class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" @click="remove(policy)">Elimina</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTableWrapper>

        <div class="space-y-3 xl:hidden">
            <article v-for="policy in filteredPolicies" :key="policy.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-950">{{ policy.number }}</p>
                        <p class="text-sm text-slate-500">{{ fullName(policy.client) }} - {{ policy.insurance_company?.name }}</p>
                    </div>
                    <StatusBadge :status="policy.status" :label="statusLabel(policy.status)" />
                </div>
                <p class="mt-3 text-sm text-slate-600">{{ policy.type }} - scadenza {{ formatDate(policy.end_date) }}</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-3">
                    <QuoteRecoveryButton :policy="policy" :companies="companies" />
                    <button class="min-h-10 rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700" @click="edit(policy)">Modifica</button>
                    <button class="min-h-10 rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700" @click="remove(policy)">Elimina</button>
                </div>
            </article>
        </div>

        <EmptyState v-if="!loading && filteredPolicies.length === 0" title="Nessuna polizza" description="Crea una nuova polizza o cambia ricerca." />
    </div>
</template>
