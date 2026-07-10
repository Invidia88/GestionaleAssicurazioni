<script setup>
import Pagination from '@/Components/Pagination.vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    companies: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(route('insurance-companies.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Compagnie" />

    <AuthenticatedLayout>
        <template #title>Compagnie</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">Compagnie assicurative</h1>
                    <p class="mt-1 text-sm text-slate-500">Contatti e riferimenti delle compagnie.</p>
                </div>
                <Link :href="route('insurance-companies.create')" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Nuova compagnia
                </Link>
            </div>
        </template>

        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Cerca per nome compagnia, referente o contatti"
                class="w-full rounded border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
        </div>

        <DataTableWrapper class="hidden md:block">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Compagnia</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Referente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Polizze</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="company in companies.data" :key="company.id">
                            <td class="px-4 py-3 font-medium text-slate-950">{{ company.name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                <p>{{ company.contact_name || '-' }}</p>
                                <p>{{ company.contact_phone || '-' }}</p>
                                <p>{{ company.contact_email || '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ company.policies_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="route('insurance-companies.show', company.id)" class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                                    Apri
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="companies.data.length === 0">
                            <td colspan="4" class="px-4 py-8">
                                <EmptyState title="Nessuna compagnia trovata" description="Aggiungi una compagnia o modifica i criteri di ricerca." />
                            </td>
                        </tr>
                    </tbody>
                </table>
        </DataTableWrapper>

        <div class="space-y-3 md:hidden">
            <article v-for="company in companies.data" :key="company.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-slate-950">{{ company.name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ company.contact_name || 'Referente non indicato' }}</p>
                    </div>
                    <span class="rounded bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">{{ company.policies_count }} polizze</span>
                </div>
                <div class="mt-3 space-y-1 text-sm text-slate-600">
                    <p>{{ company.contact_phone || 'Telefono non indicato' }}</p>
                    <p>{{ company.contact_email || 'Email non indicata' }}</p>
                </div>
                <Link :href="route('insurance-companies.show', company.id)" class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                    Apri compagnia
                </Link>
            </article>
            <EmptyState v-if="companies.data.length === 0" title="Nessuna compagnia trovata" description="Aggiungi una compagnia o modifica i criteri di ricerca." />
        </div>

        <div class="mt-4">
            <Pagination :links="companies.links" />
        </div>
    </AuthenticatedLayout>
</template>
