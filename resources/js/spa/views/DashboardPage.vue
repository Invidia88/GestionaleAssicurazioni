<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import EmptyState from '@/Components/EmptyState.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { loadClients, loadCompanies, loadPolicies } from '../lib/api.js';
import { formatDate, fullName, statusLabel, todayIso, addDaysIso } from '../lib/domain.js';

const clients = ref([]);
const companies = ref([]);
const policies = ref([]);
const loading = ref(true);
const error = ref('');

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

const expiring7 = computed(() => policies.value.filter((policy) => policy.end_date >= todayIso() && policy.end_date <= addDaysIso(todayIso(), 7)).length);
const expiring30 = computed(() => policies.value.filter((policy) => policy.end_date >= todayIso() && policy.end_date <= addDaysIso(todayIso(), 30)).length);
const expired = computed(() => policies.value.filter((policy) => policy.end_date < todayIso()).length);
const upcomingPolicies = computed(() => policies.value.filter((policy) => policy.end_date >= todayIso()).slice(0, 6));
const latestPolicies = computed(() => [...policies.value].sort((a, b) => (b.created_at ?? '').localeCompare(a.created_at ?? '')).slice(0, 6));
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-blue-700">Panoramica</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Dashboard</h2>
                <p class="mt-1 text-sm text-slate-500">Riepilogo operativo collegato a Supabase.</p>
            </div>
            <RouterLink to="/policies" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Nuova polizza
            </RouterLink>
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>
        <p v-if="loading" class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">Caricamento dati...</p>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Clienti" :value="clients.length" icon="CL" tone="blue" helper="Anagrafiche registrate" />
            <StatCard label="Compagnie" :value="companies.length" icon="CO" tone="slate" helper="Partner assicurativi" />
            <StatCard label="Polizze" :value="policies.length" icon="PL" tone="green" helper="Contratti in archivio" />
            <StatCard label="Entro 7 giorni" :value="expiring7" icon="7G" tone="amber" helper="Priorita operative" />
            <StatCard label="Entro 30 giorni" :value="expiring30" icon="30" tone="orange" helper="Da pianificare" />
            <StatCard label="Scadute" :value="expired" icon="!" tone="red" helper="Richiedono attenzione" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                    <h3 class="font-semibold text-slate-950">Prossime scadenze</h3>
                    <RouterLink to="/expirations" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Vedi tutte</RouterLink>
                </div>
                <div class="divide-y divide-slate-100">
                    <RouterLink v-for="policy in upcomingPolicies" :key="policy.id" to="/expirations" class="block px-4 py-3 hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-950">{{ fullName(policy.client) }}</p>
                                <p class="text-sm text-slate-500">{{ policy.type }} - {{ policy.insurance_company?.name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold">{{ formatDate(policy.end_date) }}</p>
                                <StatusBadge :status="policy.status" :label="statusLabel(policy.status)" />
                            </div>
                        </div>
                    </RouterLink>
                    <div v-if="!loading && upcomingPolicies.length === 0" class="p-4">
                        <EmptyState title="Nessuna scadenza futura" description="Le prossime polizze compariranno qui." />
                    </div>
                </div>
            </section>

            <section class="rounded border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                    <h3 class="font-semibold text-slate-950">Ultime polizze</h3>
                    <RouterLink to="/policies" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Archivio</RouterLink>
                </div>
                <div class="divide-y divide-slate-100">
                    <RouterLink v-for="policy in latestPolicies" :key="policy.id" to="/policies" class="block px-4 py-3 hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-950">{{ policy.number }}</p>
                                <p class="text-sm text-slate-500">{{ fullName(policy.client) }} - {{ policy.insurance_company?.name }}</p>
                            </div>
                            <StatusBadge :status="policy.status" :label="statusLabel(policy.status)" />
                        </div>
                    </RouterLink>
                    <div v-if="!loading && latestPolicies.length === 0" class="p-4">
                        <EmptyState title="Nessuna polizza" description="Inserisci la prima polizza dalla sezione Polizze." />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
