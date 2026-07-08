<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    upcomingPolicies: Array,
    latestPolicies: Array,
});

const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #title>Dashboard</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">Dashboard</h1>
                    <p class="mt-1 text-sm text-slate-500">Riepilogo operativo del portafoglio assicurativo.</p>
                </div>
                <Link :href="route('policies.create')" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                    Nuova polizza
                </Link>
            </div>
        </template>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Clienti</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.clients }}</p>
            </div>
            <div class="rounded border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Compagnie</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.companies }}</p>
            </div>
            <div class="rounded border border-slate-200 bg-white p-4">
                <p class="text-sm text-slate-500">Polizze</p>
                <p class="mt-2 text-3xl font-semibold">{{ stats.policies }}</p>
            </div>
            <div class="rounded border border-amber-200 bg-white p-4">
                <p class="text-sm text-slate-500">Entro 7 giorni</p>
                <p class="mt-2 text-3xl font-semibold text-amber-700">{{ stats.expiring7 }}</p>
            </div>
            <div class="rounded border border-orange-200 bg-white p-4">
                <p class="text-sm text-slate-500">Entro 30 giorni</p>
                <p class="mt-2 text-3xl font-semibold text-orange-700">{{ stats.expiring30 }}</p>
            </div>
            <div class="rounded border border-red-200 bg-white p-4">
                <p class="text-sm text-slate-500">Scadute</p>
                <p class="mt-2 text-3xl font-semibold text-red-700">{{ stats.expired }}</p>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="font-semibold text-slate-950">Prossime scadenze</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <Link
                        v-for="policy in upcomingPolicies"
                        :key="policy.id"
                        :href="route('policies.show', policy.id)"
                        class="block px-4 py-3 hover:bg-slate-50"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-950">{{ policy.client.full_name }}</p>
                                <p class="text-sm text-slate-500">{{ policy.type }} - {{ policy.insurance_company.name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold">{{ formatDate(policy.end_date) }}</p>
                                <StatusBadge :status="policy.status" :label="policy.status_label" />
                            </div>
                        </div>
                    </Link>
                    <p v-if="upcomingPolicies.length === 0" class="px-4 py-6 text-sm text-slate-500">
                        Nessuna scadenza futura.
                    </p>
                </div>
            </section>

            <section class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="font-semibold text-slate-950">Ultime polizze inserite</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <Link
                        v-for="policy in latestPolicies"
                        :key="policy.id"
                        :href="route('policies.show', policy.id)"
                        class="block px-4 py-3 hover:bg-slate-50"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-950">{{ policy.number }}</p>
                                <p class="text-sm text-slate-500">{{ policy.client.full_name }} - {{ policy.insurance_company.name }}</p>
                            </div>
                            <StatusBadge :status="policy.status" :label="policy.status_label" />
                        </div>
                    </Link>
                    <p v-if="latestPolicies.length === 0" class="px-4 py-6 text-sm text-slate-500">
                        Nessuna polizza inserita.
                    </p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
