<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import StatCard from '@/Components/StatCard.vue';
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
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Panoramica</p>
                    <h1 class="mt-1 text-3xl font-semibold text-slate-950">Dashboard</h1>
                    <p class="mt-1 text-sm text-slate-500">Riepilogo operativo del portafoglio assicurativo.</p>
                </div>
                <Link :href="route('policies.create')" class="inline-flex min-h-10 items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Nuova polizza
                </Link>
            </div>
        </template>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Clienti" :value="stats.clients" icon="CL" tone="blue" helper="Anagrafiche registrate" />
            <StatCard label="Compagnie" :value="stats.companies" icon="CO" tone="slate" helper="Partner assicurativi" />
            <StatCard label="Polizze" :value="stats.policies" icon="PL" tone="green" helper="Contratti in archivio" />
            <StatCard label="Entro 7 giorni" :value="stats.expiring7" icon="7G" tone="amber" helper="Priorita operative" />
            <StatCard label="Entro 30 giorni" :value="stats.expiring30" icon="30" tone="orange" helper="Da pianificare" />
            <StatCard label="Scadute" :value="stats.expired" icon="!" tone="red" helper="Richiedono attenzione" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                    <h2 class="font-semibold text-slate-950">Prossime scadenze</h2>
                    <Link :href="route('expirations.index')" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Vedi tutte</Link>
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
                    <Link :href="route('policies.index')" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Archivio</Link>
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
