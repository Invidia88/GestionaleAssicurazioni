<script setup>
import InputError from '@/Components/InputError.vue';
import FormCard from '@/Components/FormCard.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    policy: Object,
    clients: Array,
    companies: Array,
    options: Object,
});

const dateOnly = (value) => value ? String(value).slice(0, 10) : '';
const isEdit = Boolean(props.policy);
const form = useForm({
    client_id: props.policy?.client_id ?? '',
    insurance_company_id: props.policy?.insurance_company_id ?? '',
    type: props.policy?.type ?? 'Auto',
    number: props.policy?.number ?? '',
    start_date: dateOnly(props.policy?.start_date),
    end_date: dateOnly(props.policy?.end_date),
    annual_premium: props.policy?.annual_premium ?? '',
    status: props.policy?.status ?? 'attiva',
    notes: props.policy?.notes ?? '',
});

const submit = () => {
    if (isEdit) {
        form.put(route('policies.update', props.policy.id));
        return;
    }

    form.post(route('policies.store'));
};
</script>

<template>
    <Head :title="isEdit ? 'Modifica polizza' : 'Nuova polizza'" />

    <AuthenticatedLayout>
        <template #title>{{ isEdit ? 'Modifica polizza' : 'Nuova polizza' }}</template>

        <template #header>
            <div>
                <h1 class="text-2xl font-semibold text-slate-950">{{ isEdit ? 'Modifica polizza' : 'Nuova polizza' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Collega cliente, compagnia, premio e scadenza.</p>
            </div>
        </template>

        <FormCard title="Dati polizza" description="Mantieni aggiornate scadenza, premio, stato e riferimenti.">
            <form @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Cliente</label>
                    <select v-model="form.client_id" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleziona cliente</option>
                        <option v-for="client in clients" :key="client.id" :value="client.id">
                            {{ client.last_name }} {{ client.first_name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.client_id" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Compagnia</label>
                    <select v-model="form.insurance_company_id" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleziona compagnia</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">
                            {{ company.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.insurance_company_id" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Tipo polizza</label>
                    <select v-model="form.type" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option v-for="type in options.types" :key="type" :value="type">{{ type }}</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Numero polizza</label>
                    <input v-model="form.number" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.number" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Data decorrenza</label>
                    <input v-model="form.start_date" type="date" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.start_date" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Data scadenza</label>
                    <input v-model="form.end_date" type="date" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.end_date" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Premio annuale</label>
                    <input v-model="form.annual_premium" type="number" min="0" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <InputError :message="form.errors.annual_premium" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Stato</label>
                    <select v-model="form.status" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option v-for="status in options.statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="4" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Link :href="isEdit ? route('policies.show', policy.id) : route('policies.index')" class="inline-flex min-h-10 w-full items-center justify-center rounded border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 sm:w-auto">
                    Annulla
                </Link>
                <PrimaryButton type="submit" :disabled="form.processing" class="w-full sm:w-auto">
                    Salva
                </PrimaryButton>
            </div>
        </form>
        </FormCard>
    </AuthenticatedLayout>
</template>
