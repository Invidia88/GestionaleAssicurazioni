<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import QuoteRecoveryButton from '../components/QuoteRecoveryButton.vue';
import { loadCompanies, loadPolicies } from '../lib/api.js';
import { addDaysIso, formatDate, fullName, policyStatuses, policyTypes, statusLabel, todayIso, whatsappUrl } from '../lib/domain.js';

const companies = ref([]);
const policies = ref([]);
const loading = ref(true);
const error = ref('');

const filters = reactive({
    search: '',
    range: '',
    company_id: '',
    type: '',
    status: '',
});

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const [companyRows, policyRows] = await Promise.all([loadCompanies(), loadPolicies()]);
        companies.value = companyRows;
        policies.value = policyRows;
    } catch (exception) {
        error.value = exception.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const setRange = (range) => {
    filters.range = range;
};

const resetFilters = () => {
    Object.assign(filters, {
        search: '',
        range: '',
        company_id: '',
        type: '',
        status: '',
    });
};

const filteredPolicies = computed(() => {
    const term = filters.search.toLowerCase().trim();

    return policies.value
        .filter((policy) => {
            if (filters.company_id && policy.insurance_company_id !== filters.company_id) return false;
            if (filters.type && policy.type !== filters.type) return false;
            if (filters.status && policy.status !== filters.status) return false;

            if (filters.range === 'today' && policy.end_date !== todayIso()) return false;
            if (filters.range === '7' && (policy.end_date < todayIso() || policy.end_date > addDaysIso(todayIso(), 7))) return false;
            if (filters.range === '30' && (policy.end_date < todayIso() || policy.end_date > addDaysIso(todayIso(), 30))) return false;
            if (filters.range === 'expired' && policy.end_date >= todayIso()) return false;

            if (!term) return true;

            return [policy.number, policy.type, fullName(policy.client), policy.client?.phone, policy.client?.tax_code, policy.insurance_company?.name]
                .filter(Boolean)
                .some((value) => value.toLowerCase().includes(term));
        })
        .sort((a, b) => a.end_date.localeCompare(b.end_date));
});

const visibleExpired = computed(() => filteredPolicies.value.filter((policy) => policy.end_date < todayIso()).length);
const visibleExpiring = computed(() => filteredPolicies.value.filter((policy) => policy.end_date >= todayIso() && policy.end_date <= addDaysIso(todayIso(), 30)).length);

const rowClass = (policy) => {
    if (policy.end_date < todayIso()) {
        return 'bg-red-50/60 hover:bg-red-50';
    }

    if (policy.end_date <= addDaysIso(todayIso(), 30)) {
        return 'bg-amber-50/60 hover:bg-amber-50';
    }

    return '';
};
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-blue-700">Priorita operative</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Scadenziario</h2>
                <p class="mt-1 text-sm text-slate-500">Scadenze ordinate, reminder WhatsApp e preventivi recupero.</p>
            </div>
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Risultati" :value="filteredPolicies.length" icon="SC" tone="blue" helper="Scadenze filtrate" />
            <StatCard label="In archivio" :value="policies.length" icon="PL" tone="slate" helper="Polizze totali" />
            <StatCard label="In scadenza" :value="visibleExpiring" icon="!" tone="amber" helper="Entro 30 giorni" />
            <StatCard label="Scadute" :value="visibleExpired" icon="X" tone="red" helper="Da recuperare" />
        </div>

        <form class="rounded border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/70" @submit.prevent>
            <div class="grid gap-3 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Ricerca</label>
                    <input v-model="filters.search" type="search" placeholder="Cliente, telefono, codice fiscale o numero" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Compagnia</label>
                    <select v-model="filters.company_id" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutte</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipo</label>
                    <select v-model="filters.type" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutti</option>
                        <option v-for="type in policyTypes" :key="type" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Stato</label>
                    <select v-model="filters.status" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutti</option>
                        <option v-for="status in policyStatuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" class="min-h-10 w-full rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="resetFilters">
                        Reset
                    </button>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" @click="setRange('today')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', filters.range === 'today' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700']">Oggi</button>
                <button type="button" @click="setRange('7')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', filters.range === '7' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700']">Entro 7 giorni</button>
                <button type="button" @click="setRange('30')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', filters.range === '30' ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-700']">Entro 30 giorni</button>
                <button type="button" @click="setRange('expired')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', filters.range === 'expired' ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-700']">Scadute</button>
            </div>
        </form>

        <DataTableWrapper class="hidden xl:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Scadenza</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Polizza</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Stato</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="policy in filteredPolicies" :key="policy.id" :class="rowClass(policy)">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-950">{{ formatDate(policy.end_date) }}</td>
                        <td class="px-4 py-3 text-sm">
                            <p class="font-medium text-slate-950">{{ fullName(policy.client) }}</p>
                            <p class="text-slate-500">{{ policy.client?.phone || '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            <p class="font-medium text-slate-950">{{ policy.number }}</p>
                            <p>{{ policy.type }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ policy.insurance_company?.name }}</td>
                        <td class="px-4 py-3"><StatusBadge :status="policy.status" :label="statusLabel(policy.status)" /></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <WhatsAppButton :href="whatsappUrl(policy)" />
                                <QuoteRecoveryButton :policy="policy" :companies="companies" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTableWrapper>

        <div class="space-y-3 xl:hidden">
            <article
                v-for="policy in filteredPolicies"
                :key="policy.id"
                :class="['rounded border bg-white p-4 shadow-sm', policy.end_date < todayIso() ? 'border-red-200 bg-red-50/60' : policy.end_date <= addDaysIso(todayIso(), 30) ? 'border-amber-200 bg-amber-50/60' : 'border-slate-200']"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ formatDate(policy.end_date) }}</p>
                        <p class="mt-1 font-semibold text-slate-950">{{ fullName(policy.client) }}</p>
                    </div>
                    <StatusBadge :status="policy.status" :label="statusLabel(policy.status)" />
                </div>
                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    <p>{{ policy.number }} - {{ policy.type }}</p>
                    <p>{{ policy.insurance_company?.name }}</p>
                    <p>{{ policy.client?.phone || 'Telefono non indicato' }}</p>
                </div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <WhatsAppButton :href="whatsappUrl(policy)" />
                    <QuoteRecoveryButton :policy="policy" :companies="companies" />
                </div>
            </article>
        </div>

        <EmptyState v-if="!loading && filteredPolicies.length === 0" title="Nessuna scadenza trovata" description="Prova a cambiare filtri o ricerca." />
    </div>
</template>
