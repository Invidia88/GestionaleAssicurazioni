<script setup>
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StatCard from '@/Components/StatCard.vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import CreateQuoteButton from '@/Components/CreateQuoteButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    policies: Object,
    filters: Object,
    companies: Array,
    options: Object,
});

const form = useForm({
    search: props.filters.search ?? '',
    range: props.filters.range ?? '',
    company_id: props.filters.company_id ?? '',
    type: props.filters.type ?? '',
    status: props.filters.status ?? '',
});

const submit = () => {
    router.get(route('expirations.index'), form.data(), {
        preserveState: true,
        replace: true,
    });
};

const setRange = (range) => {
    form.range = range;
    submit();
};

const resetFilters = () => {
    form.search = '';
    form.range = '';
    form.company_id = '';
    form.type = '';
    form.status = '';
    submit();
};

const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));
const visibleExpired = computed(() => props.policies.data.filter((policy) => policy.status === 'scaduta').length);
const visibleExpiring = computed(() => props.policies.data.filter((policy) => policy.status === 'in_scadenza').length);
const rowClass = (policy) => {
    if (policy.status === 'scaduta') {
        return 'bg-red-50/60 hover:bg-red-50';
    }

    if (policy.status === 'in_scadenza') {
        return 'bg-amber-50/60 hover:bg-amber-50';
    }

    return '';
};
</script>

<template>
    <Head title="Scadenziario" />

    <AuthenticatedLayout>
        <template #title>Scadenziario</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Priorita operative</p>
                    <h1 class="mt-1 text-3xl font-semibold text-slate-950">Scadenziario</h1>
                    <p class="mt-1 text-sm text-slate-500">Polizze ordinate per data scadenza con filtri rapidi.</p>
                </div>
                <Link :href="route('policies.create')" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Nuova polizza
                </Link>
            </div>
        </template>

        <div class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Risultati visibili" :value="policies.total" icon="SC" tone="blue" helper="Scadenze filtrate" />
            <StatCard label="In pagina" :value="policies.data.length" icon="PG" tone="slate" helper="Elementi caricati" />
            <StatCard label="In scadenza" :value="visibleExpiring" icon="!" tone="amber" helper="Da contattare" />
            <StatCard label="Scadute" :value="visibleExpired" icon="X" tone="red" helper="Priorita alta" />
        </div>

        <form @submit.prevent="submit" class="mb-5 rounded border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/70">
            <div class="grid gap-3 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Ricerca</label>
                    <input v-model="form.search" type="search" placeholder="Cliente, telefono, codice fiscale o numero" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Compagnia</label>
                    <select v-model="form.company_id" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutte</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipo</label>
                    <select v-model="form.type" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutti</option>
                        <option v-for="type in options.types" :key="type" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Stato</label>
                    <select v-model="form.status" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tutti</option>
                        <option v-for="status in options.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="min-h-10 w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Filtra
                    </button>
                    <button type="button" @click="resetFilters" class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                        Reset
                    </button>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" @click="setRange('today')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', form.range === 'today' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700']">Oggi</button>
                <button type="button" @click="setRange('7')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', form.range === '7' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-700']">Entro 7 giorni</button>
                <button type="button" @click="setRange('30')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', form.range === '30' ? 'bg-orange-600 text-white' : 'bg-slate-100 text-slate-700']">Entro 30 giorni</button>
                <button type="button" @click="setRange('expired')" :class="['min-h-10 rounded px-3 py-2 text-sm font-semibold', form.range === 'expired' ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-700']">Scadute</button>
            </div>
        </form>

        <DataTableWrapper class="hidden lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
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
                        <tr v-for="policy in policies.data" :key="policy.id" :class="rowClass(policy)">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-950">{{ formatDate(policy.end_date) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <Link :href="route('clients.show', policy.client.id)" class="font-medium text-slate-950 hover:text-blue-700">
                                    {{ policy.client.full_name }}
                                </Link>
                                <p class="text-slate-500">{{ policy.client.phone || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <p class="font-medium text-slate-950">{{ policy.number }}</p>
                                <p>{{ policy.type }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ policy.insurance_company.name }}</td>
                            <td class="px-4 py-3"><StatusBadge :status="policy.status" :label="policy.status_label" /></td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <WhatsAppButton :href="policy.whatsapp_url" />
                                    <CreateQuoteButton :policy="policy" :companies="companies" />
                                    <Link :href="route('policies.show', policy.id)" class="inline-flex min-h-10 items-center rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Apri</Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="policies.data.length === 0">
                            <td colspan="6" class="px-4 py-8">
                                <EmptyState title="Nessuna scadenza trovata" description="Prova a cambiare filtri o ricerca." />
                            </td>
                        </tr>
                    </tbody>
                </table>
        </DataTableWrapper>

        <div class="space-y-3 lg:hidden">
            <article
                v-for="policy in policies.data"
                :key="policy.id"
                :class="[
                    'rounded border bg-white p-4 shadow-sm',
                    policy.status === 'scaduta' ? 'border-red-200 bg-red-50/60' : policy.status === 'in_scadenza' ? 'border-amber-200 bg-amber-50/60' : 'border-slate-200',
                ]"
            >
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ formatDate(policy.end_date) }}</p>
                        <p class="mt-1 font-semibold text-slate-950">{{ policy.client.full_name }}</p>
                    </div>
                    <StatusBadge :status="policy.status" :label="policy.status_label" />
                </div>
                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    <p>{{ policy.number }} - {{ policy.type }}</p>
                    <p>{{ policy.insurance_company.name }}</p>
                    <p>{{ policy.client.phone || 'Telefono non indicato' }}</p>
                </div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <WhatsAppButton :href="policy.whatsapp_url" />
                    <CreateQuoteButton :policy="policy" :companies="companies" />
                    <Link :href="route('policies.show', policy.id)" class="inline-flex min-h-10 items-center justify-center rounded border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">
                        Apri polizza
                    </Link>
                </div>
            </article>
            <EmptyState v-if="policies.data.length === 0" title="Nessuna scadenza trovata" description="Prova a cambiare filtri o ricerca." />
        </div>

        <div class="mt-4">
            <Pagination :links="policies.links" />
        </div>
    </AuthenticatedLayout>
</template>
