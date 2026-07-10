<script setup>
import Pagination from '@/Components/Pagination.vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    clients: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(route('clients.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Clienti" />

    <AuthenticatedLayout>
        <template #title>Clienti</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">Clienti</h1>
                    <p class="mt-1 text-sm text-slate-500">Anagrafica clienti e contatti principali.</p>
                </div>
                <Link :href="route('clients.create')" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Nuovo cliente
                </Link>
            </div>
        </template>

        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Cerca per nome, telefono, email o codice fiscale"
                class="w-full rounded border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <DataTableWrapper class="hidden md:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Contatti</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Citta</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Polizze</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="client in clients.data" :key="client.id">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-950">{{ client.full_name }}</p>
                                <p class="text-sm text-slate-500">{{ client.tax_code || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <p>{{ client.phone || '-' }}</p>
                                <p>{{ client.email || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ client.city || '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ client.policies_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="route('clients.show', client.id)" class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                                    Apri
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="clients.data.length === 0">
                            <td colspan="5" class="px-4 py-8">
                                <EmptyState title="Nessun cliente trovato" description="Modifica la ricerca oppure aggiungi un nuovo cliente." />
                            </td>
                        </tr>
                    </tbody>
                </table>
        </DataTableWrapper>

        <div class="space-y-3 md:hidden">
            <article v-for="client in clients.data" :key="client.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-950">{{ client.full_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ client.tax_code || 'Codice fiscale non indicato' }}</p>
                    </div>
                    <span class="rounded bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">{{ client.policies_count }} polizze</span>
                </div>
                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    <p>{{ client.phone || 'Telefono non indicato' }}</p>
                    <p>{{ client.email || 'Email non indicata' }}</p>
                    <p>{{ client.city || 'Citta non indicata' }}</p>
                </div>
                <Link :href="route('clients.show', client.id)" class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                    Apri cliente
                </Link>
            </article>
            <EmptyState v-if="clients.data.length === 0" title="Nessun cliente trovato" description="Modifica la ricerca oppure aggiungi un nuovo cliente." />
        </div>

        <div class="mt-4">
            <Pagination :links="clients.links" />
        </div>
    </AuthenticatedLayout>
</template>
