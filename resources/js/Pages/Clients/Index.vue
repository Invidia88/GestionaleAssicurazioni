<script setup>
import Pagination from '@/Components/Pagination.vue';
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
                <Link :href="route('clients.create')" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                    Nuovo cliente
                </Link>
            </div>
        </template>

        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Cerca per nome, telefono, email o codice fiscale"
                class="w-full rounded border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"
            >
        </div>

        <div class="overflow-hidden rounded border border-slate-200 bg-white">
            <div class="overflow-x-auto">
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
                                <Link :href="route('clients.show', client.id)" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                    Apri
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="clients.data.length === 0">
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Nessun cliente trovato.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <Pagination :links="clients.links" />
        </div>
    </AuthenticatedLayout>
</template>
