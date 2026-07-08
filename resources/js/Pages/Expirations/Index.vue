<script setup>
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

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
</script>

<template>
    <Head title="Scadenziario" />

    <AuthenticatedLayout>
        <template #title>Scadenziario</template>

        <template #header>
            <div>
                <h1 class="text-2xl font-semibold text-slate-950">Scadenziario</h1>
                <p class="mt-1 text-sm text-slate-500">Polizze ordinate per data scadenza con filtri rapidi.</p>
            </div>
        </template>

        <form @submit.prevent="submit" class="mb-5 rounded border border-slate-200 bg-white p-4">
            <div class="grid gap-3 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Ricerca</label>
                    <input v-model="form.search" type="search" placeholder="Cliente, telefono, codice fiscale o numero" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Compagnia</label>
                    <select v-model="form.company_id" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Tutte</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipo</label>
                    <select v-model="form.type" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Tutti</option>
                        <option v-for="type in options.types" :key="type" :value="type">{{ type }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Stato</label>
                    <select v-model="form.status" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Tutti</option>
                        <option v-for="status in options.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                        Filtra
                    </button>
                    <button type="button" @click="resetFilters" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Reset
                    </button>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" @click="setRange('today')" :class="['rounded px-3 py-2 text-sm font-semibold', form.range === 'today' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700']">Oggi</button>
                <button type="button" @click="setRange('7')" :class="['rounded px-3 py-2 text-sm font-semibold', form.range === '7' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700']">Entro 7 giorni</button>
                <button type="button" @click="setRange('30')" :class="['rounded px-3 py-2 text-sm font-semibold', form.range === '30' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700']">Entro 30 giorni</button>
                <button type="button" @click="setRange('expired')" :class="['rounded px-3 py-2 text-sm font-semibold', form.range === 'expired' ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-700']">Scadute</button>
            </div>
        </form>

        <div class="overflow-hidden rounded border border-slate-200 bg-white">
            <div class="overflow-x-auto">
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
                        <tr v-for="policy in policies.data" :key="policy.id">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-950">{{ formatDate(policy.end_date) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <Link :href="route('clients.show', policy.client.id)" class="font-medium text-slate-950 hover:text-emerald-700">
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
                                <div class="flex justify-end gap-3">
                                    <a v-if="policy.whatsapp_url" :href="policy.whatsapp_url" target="_blank" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">WhatsApp</a>
                                    <Link :href="route('policies.show', policy.id)" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Apri</Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="policies.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Nessuna scadenza trovata.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <Pagination :links="policies.links" />
        </div>
    </AuthenticatedLayout>
</template>
