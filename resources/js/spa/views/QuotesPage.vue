<script setup>
import { computed, onMounted, ref } from 'vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { deleteRecord, loadQuotes } from '../lib/api.js';
import { formatCurrency, formatDate, fullName, quoteStatusLabel } from '../lib/domain.js';

const quotes = ref([]);
const search = ref('');
const loading = ref(true);
const error = ref('');

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        quotes.value = await loadQuotes();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const filteredQuotes = computed(() => {
    const term = search.value.toLowerCase().trim();

    if (!term) {
        return quotes.value;
    }

    return quotes.value.filter((quote) =>
        [quote.number, quote.type, quote.status, fullName(quote.client), quote.insurance_company?.name]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

const remove = async (quote) => {
    if (!confirm(`Eliminare il preventivo ${quote.number}?`)) {
        return;
    }

    try {
        await deleteRecord('quotes', quote.id);
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
                <p class="text-xs font-semibold uppercase text-blue-700">Recupero clienti</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Preventivi</h2>
                <p class="mt-1 text-sm text-slate-500">Preventivi creati dalle polizze scadute oltre 15 giorni.</p>
            </div>
            <input v-model="search" type="search" placeholder="Cerca preventivo" class="min-h-10 rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>

        <DataTableWrapper class="hidden lg:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Preventivo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Premio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Stato</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="quote in filteredQuotes" :key="quote.id">
                        <td class="px-4 py-3 text-sm">
                            <p class="font-semibold text-slate-950">{{ quote.number }}</p>
                            <p class="text-slate-500">{{ quote.type }} - {{ formatDate(quote.created_at?.slice(0, 10)) }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ fullName(quote.client) }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ quote.insurance_company?.name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ quote.annual_premium ? formatCurrency(quote.annual_premium) : '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-700">{{ quoteStatusLabel(quote.status) }}</td>
                        <td class="px-4 py-3 text-right">
                            <button class="rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" @click="remove(quote)">Elimina</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTableWrapper>

        <div class="space-y-3 lg:hidden">
            <article v-for="quote in filteredQuotes" :key="quote.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-slate-950">{{ quote.number }}</p>
                        <p class="text-sm text-slate-500">{{ fullName(quote.client) }}</p>
                    </div>
                    <span class="rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-800">{{ quoteStatusLabel(quote.status) }}</span>
                </div>
                <p class="mt-3 text-sm text-slate-600">{{ quote.type }} - {{ quote.insurance_company?.name }}</p>
                <button class="mt-4 min-h-10 w-full rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700" @click="remove(quote)">Elimina</button>
            </article>
        </div>

        <EmptyState v-if="!loading && filteredQuotes.length === 0" title="Nessun preventivo" description="I preventivi di recupero creati dallo scadenziario compariranno qui." />
    </div>
</template>
