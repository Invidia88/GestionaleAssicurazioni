<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import EmptyState from '@/Components/EmptyState.vue';
import DangerButton from '@/Components/DangerButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    company: Object,
});

const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));

const destroyCompany = () => {
    if (confirm('Eliminare questa compagnia e le sue polizze?')) {
        router.delete(route('insurance-companies.destroy', props.company.id));
    }
};
</script>

<template>
    <Head :title="company.name" />

    <AuthenticatedLayout>
        <template #title>{{ company.name }}</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">{{ company.name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ company.contact_name || 'Referente non indicato' }}</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <Link :href="route('insurance-companies.edit', company.id)" class="inline-flex min-h-10 items-center justify-center rounded border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Modifica
                    </Link>
                    <DangerButton @click="destroyCompany" type="button">
                        Elimina
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded border border-slate-200 bg-white p-5 xl:col-span-1">
                <h2 class="font-semibold text-slate-950">Dettagli compagnia</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-slate-500">Referente</dt><dd class="font-medium">{{ company.contact_name || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Telefono</dt><dd class="font-medium">{{ company.contact_phone || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ company.contact_email || '-' }}</dd></div>
                    <div><dt class="text-slate-500">Note</dt><dd class="whitespace-pre-line font-medium">{{ company.notes || '-' }}</dd></div>
                </dl>
            </section>

            <section class="rounded border border-slate-200 bg-white xl:col-span-2">
                <div class="border-b border-slate-200 px-4 py-3">
                    <h2 class="font-semibold text-slate-950">Polizze della compagnia</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="policy in company.policies" :key="policy.id">
                                <td class="px-4 py-3">
                                    <Link :href="route('policies.show', policy.id)" class="font-medium text-slate-950 hover:text-blue-700">
                                        {{ policy.number }}
                                    </Link>
                                    <p class="text-sm text-slate-500">{{ policy.type }} - {{ policy.client.full_name }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ formatDate(policy.end_date) }}</td>
                                <td class="px-4 py-3"><StatusBadge :status="policy.status" :label="policy.status_label" /></td>
                            </tr>
                            <tr v-if="company.policies.length === 0">
                                <td class="px-4 py-8">
                                    <EmptyState title="Nessuna polizza associata" description="Le polizze della compagnia appariranno qui." />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
