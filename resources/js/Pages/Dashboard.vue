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
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Panoramica</p>
                    <h1 class="mt-1 text-3xl font-semibold text-slate-950">Dashboard</h1>
                    <p class="mt-1 text-sm text-slate-500">Riepilogo operativo del portafoglio assicurativo.</p>
                </div>
                <Link :href="route('policies.create')" class="rounded bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Nuova polizza
                </Link>
            </div>
        </template>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-slate-500">Clienti</p>
                    <span class="rounded bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">CL</span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ stats.clients }}</p>
            </div>
            <div class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-slate-500">Compagnie</p>
                    <span class="rounded bg-cyan-50 px-2 py-1 text-xs font-bold text-cyan-700">CO</span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ stats.companies }}</p>
            </div>
            <div class="rounded border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between">
                    <p class="text-sm font-medium text-slate-500">Polizze</p>
                    <span class="rounded bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700">PL</span>
                </div>
                <p class="mt-4 text-3xl font-semibold text-slate-950">{{ stats.policies }}</p>
            </div>
            <div class="rounded border border-amber-200 bg-white p-4">
                <p class="text-sm font-medium text-slate-500">Entro 7 giorni</p>
                <p class="mt-4 text-3xl font-semibold text-amber-700">{{ stats.expiring7 }}</p>
                <div class="mt-3 h-1 rounded bg-amber-100"><div class="h-1 w-2/3 rounded bg-amber-500"></div></div>
            </div>
            <div class="rounded border border-orange-200 bg-white p-4">
                <p class="text-sm font-medium text-slate-500">Entro 30 giorni</p>
                <p class="mt-4 text-3xl font-semibold text-orange-700">{{ stats.expiring30 }}</p>
                <div class="mt-3 h-1 rounded bg-orange-100"><div class="h-1 w-3/4 rounded bg-orange-500"></div></div>
            </div>
            <div class="rounded border border-red-200 bg-white p-4">
                <p class="text-sm font-medium text-slate-500">Scadute</p>
                <p class="mt-4 text-3xl font-semibold text-red-700">{{ stats.expired }}</p>
                <div class="mt-3 h-1 rounded bg-red-100"><div class="h-1 w-1/2 rounded bg-red-500"></div></div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                    <h2 class="font-semibold text-slate-950">Prossime scadenze</h2>
                    <Link :href="route('expirations.index')" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Vedi tutte</Link>
                </div>
                <div class="divide-y divide-slate-100">
                    <Link
                        v-for="policy in upcomingPolicies"
                        :key="policy.id"
                        :href="route('policies.show', policy.id)"
                        class="block px-4 py-3 hover:bg-slate-50"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-950">{{ policy.client.full_name }}</p>
                                <p class="text-sm text-slate-500">{{ policy.type }} - {{ policy.insurance_company.name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
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
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                    <h2 class="font-semibold text-slate-950">Ultime polizze inserite</h2>
                    <Link :href="route('policies.index')" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Archivio</Link>
                </div>
                <div class="divide-y divide-slate-100">
                    <Link
                        v-for="policy in latestPolicies"
                        :key="policy.id"
                        :href="route('policies.show', policy.id)"
                        class="block px-4 py-3 hover:bg-slate-50"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
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
