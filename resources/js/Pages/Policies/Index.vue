<script setup>
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import CreateQuoteButton from '@/Components/CreateQuoteButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    policies: Object,
    filters: Object,
    companies: Array,
});

const search = ref(props.filters.search ?? '');
const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));
const formatCurrency = (value) => new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' }).format(value);

watch(search, (value) => {
    router.get(route('policies.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Polizze" />

    <AuthenticatedLayout>
        <template #title>Polizze</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">Polizze</h1>
                    <p class="mt-1 text-sm text-slate-500">Gestione completa delle coperture assicurative.</p>
                </div>
                <Link :href="route('policies.create')" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Nuova polizza
                </Link>
            </div>
        </template>

        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Cerca per cliente, telefono, codice fiscale o numero polizza"
                class="w-full rounded border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <DataTableWrapper class="hidden lg:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Polizza</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Scadenza</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Premio</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="policy in policies.data" :key="policy.id">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-950">{{ policy.number }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    <span class="text-sm text-slate-500">{{ policy.type }}</span>
                                    <StatusBadge :status="policy.status" :label="policy.status_label" />
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <Link :href="route('clients.show', policy.client.id)" class="font-medium text-slate-900 hover:text-blue-700">
                                    {{ policy.client.full_name }}
                                </Link>
                                <p>{{ policy.client.phone || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ policy.insurance_company.name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ formatDate(policy.end_date) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ formatCurrency(policy.annual_premium) }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <WhatsAppButton :href="policy.whatsapp_url" />
                                    <CreateQuoteButton :policy="policy" :companies="companies" />
                                    <Link :href="route('policies.show', policy.id)" class="inline-flex min-h-10 items-center rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                        Apri
                                    </Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="policies.data.length === 0">
                            <td colspan="6" class="px-4 py-8">
                                <EmptyState title="Nessuna polizza trovata" description="Modifica la ricerca o inserisci una nuova polizza." />
                            </td>
                        </tr>
                    </tbody>
                </table>
        </DataTableWrapper>

        <div class="space-y-3 lg:hidden">
            <article v-for="policy in policies.data" :key="policy.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-950">{{ policy.number }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ policy.type }} - {{ policy.insurance_company.name }}</p>
                    </div>
                    <StatusBadge :status="policy.status" :label="policy.status_label" />
                </div>
                <div class="mt-3 grid gap-2 text-sm text-slate-600">
                    <p><span class="font-medium text-slate-800">Cliente:</span> {{ policy.client.full_name }}</p>
                    <p><span class="font-medium text-slate-800">Scadenza:</span> {{ formatDate(policy.end_date) }}</p>
                    <p><span class="font-medium text-slate-800">Premio:</span> {{ formatCurrency(policy.annual_premium) }}</p>
                </div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <WhatsAppButton :href="policy.whatsapp_url" />
                    <CreateQuoteButton :policy="policy" :companies="companies" />
                    <Link :href="route('policies.show', policy.id)" class="inline-flex min-h-10 items-center justify-center rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">
                        Apri polizza
                    </Link>
                </div>
            </article>
            <EmptyState v-if="policies.data.length === 0" title="Nessuna polizza trovata" description="Modifica la ricerca o inserisci una nuova polizza." />
        </div>

        <div class="mt-4">
            <Pagination :links="policies.links" />
        </div>
    </AuthenticatedLayout>
</template>
