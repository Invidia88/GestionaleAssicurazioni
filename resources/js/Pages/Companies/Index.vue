<script setup>
import Pagination from '@/Components/Pagination.vue';
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
                <Link :href="route('insurance-companies.create')" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                    Nuova compagnia
                </Link>
            </div>
        </template>

        <div class="mb-4">
            <input
                v-model="search"
                type="search"
                placeholder="Cerca per nome compagnia, referente o contatti"
                class="w-full rounded border-slate-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"
            >
        </div>

        <div class="overflow-hidden rounded border border-slate-200 bg-white">
            <div class="overflow-x-auto">
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
                                <Link :href="route('insurance-companies.show', company.id)" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                                    Apri
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="companies.data.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Nessuna compagnia trovata.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <Pagination :links="companies.links" />
        </div>
    </AuthenticatedLayout>
</template>
