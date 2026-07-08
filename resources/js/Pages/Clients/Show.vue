<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
});

const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));

const destroyClient = () => {
    if (confirm('Eliminare questo cliente e le sue polizze?')) {
        router.delete(route('clients.destroy', props.client.id));
    }
};
</script>

<template>
    <Head :title="client.full_name" />

    <AuthenticatedLayout>
        <template #title>{{ client.full_name }}</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">{{ client.full_name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ client.tax_code || 'Codice fiscale non indicato' }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('clients.edit', client.id)" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Modifica
                    </Link>
                    <button @click="destroyClient" type="button" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                        Elimina
                    </button>
                </div>
            </div>
        </template>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded border border-slate-200 bg-white p-5 xl:col-span-1">
                <h2 class="font-semibold text-slate-950">Dettagli cliente</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-slate-500">Telefono</dt><dd class="font-medium">{{ client.phone || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ client.email || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Indirizzo</dt><dd class="font-medium">{{ client.address || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Citta</dt><dd class="font-medium">{{ client.city || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Note</dt><dd class="whitespace-pre-line font-medium">{{ client.notes || '-' }}</dd></div>
                </dl>
            </section>

            <section class="rounded border border-slate-200 bg-white xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h2 class="font-semibold text-slate-950">Polizze associate</h2>
                    <Link :href="route('policies.create')" class="text-sm font-semibold text-emerald-700">Nuova polizza</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="policy in client.policies" :key="policy.id">
                                <td class="px-4 py-3">
                                    <Link :href="route('policies.show', policy.id)" class="font-medium text-slate-950 hover:text-emerald-700">
                                        {{ policy.number }}
                                    </Link>
                                    <p class="text-sm text-slate-500">{{ policy.type }} - {{ policy.insurance_company.name }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ formatDate(policy.end_date) }}</td>
                                <td class="px-4 py-3"><StatusBadge :status="policy.status" :label="policy.status_label" /></td>
                            </tr>
                            <tr v-if="client.policies.length === 0">
                                <td class="px-4 py-8 text-center text-sm text-slate-500">Nessuna polizza associata.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
