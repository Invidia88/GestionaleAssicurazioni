<script setup>
import StatusBadge from '@/Components/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    policy: Object,
});

const formatDate = (date) => new Intl.DateTimeFormat('it-IT').format(new Date(date));
const formatCurrency = (value) => new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' }).format(value);

const destroyPolicy = () => {
    if (confirm('Eliminare questa polizza?')) {
        router.delete(route('policies.destroy', props.policy.id));
    }
};
</script>

<template>
    <Head :title="policy.number" />

    <AuthenticatedLayout>
        <template #title>{{ policy.number }}</template>

        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-950">{{ policy.number }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-slate-500">{{ policy.type }}</span>
                        <StatusBadge :status="policy.status" :label="policy.status_label" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a v-if="policy.whatsapp_url" :href="policy.whatsapp_url" target="_blank" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">
                        Invia promemoria WhatsApp
                    </a>
                    <Link :href="route('policies.edit', policy.id)" class="rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Modifica
                    </Link>
                    <button @click="destroyPolicy" type="button" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">
                        Elimina
                    </button>
                </div>
            </div>
        </template>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded border border-slate-200 bg-white p-5 xl:col-span-2">
                <h2 class="font-semibold text-slate-950">Dati polizza</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Cliente</dt><dd class="font-medium"><Link :href="route('clients.show', policy.client.id)" class="hover:text-emerald-700">{{ policy.client.full_name }}</Link></dd></div>
                    <div><dt class="text-slate-500">Compagnia</dt><dd class="font-medium"><Link :href="route('insurance-companies.show', policy.insurance_company.id)" class="hover:text-emerald-700">{{ policy.insurance_company.name }}</Link></dd></div>
                    <div><dt class="text-slate-500">Decorrenza</dt><dd class="font-medium">{{ formatDate(policy.start_date) }}</dd></div>
                    <div><dt class="text-slate-500">Scadenza</dt><dd class="font-medium">{{ formatDate(policy.end_date) }}</dd></div>
                    <div><dt class="text-slate-500">Premio annuale</dt><dd class="font-medium">{{ formatCurrency(policy.annual_premium) }}</dd></div>
                    <div><dt class="text-slate-500">Telefono cliente</dt><dd class="font-medium">{{ policy.client.phone || '-' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Note</dt><dd class="whitespace-pre-line font-medium">{{ policy.notes || '-' }}</dd></div>
                </dl>
            </section>

            <aside class="rounded border border-slate-200 bg-white p-5">
                <h2 class="font-semibold text-slate-950">Promemoria</h2>
                <p class="mt-3 text-sm text-slate-600">
                    Il pulsante WhatsApp usa il telefono del cliente e prepara il messaggio con tipo polizza, compagnia e data scadenza.
                </p>
                <a v-if="policy.whatsapp_url" :href="policy.whatsapp_url" target="_blank" class="mt-4 block rounded bg-emerald-700 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-emerald-800">
                    Apri WhatsApp
                </a>
                <p v-else class="mt-4 rounded bg-amber-50 px-3 py-2 text-sm text-amber-800">
                    Inserisci un telefono cliente per abilitare WhatsApp.
                </p>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
