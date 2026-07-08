<script setup>
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
});

const isEdit = Boolean(props.client);
const form = useForm({
    first_name: props.client?.first_name ?? '',
    last_name: props.client?.last_name ?? '',
    phone: props.client?.phone ?? '',
    email: props.client?.email ?? '',
    tax_code: props.client?.tax_code ?? '',
    address: props.client?.address ?? '',
    city: props.client?.city ?? '',
    notes: props.client?.notes ?? '',
});

const submit = () => {
    if (isEdit) {
        form.put(route('clients.update', props.client.id));
        return;
    }

    form.post(route('clients.store'));
};
</script>

<template>
    <Head :title="isEdit ? 'Modifica cliente' : 'Nuovo cliente'" />

    <AuthenticatedLayout>
        <template #title>{{ isEdit ? 'Modifica cliente' : 'Nuovo cliente' }}</template>

        <template #header>
            <div>
                <h1 class="text-2xl font-semibold text-slate-950">{{ isEdit ? 'Modifica cliente' : 'Nuovo cliente' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Inserisci i dati anagrafici e i recapiti.</p>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl rounded border border-slate-200 bg-white p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Nome</label>
                    <input v-model="form.first_name" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.first_name" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Cognome</label>
                    <input v-model="form.last_name" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.last_name" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Telefono</label>
                    <input v-model="form.phone" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Email</label>
                    <input v-model="form.email" type="email" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Codice fiscale</label>
                    <input v-model="form.tax_code" type="text" class="mt-1 w-full rounded border-slate-300 text-sm uppercase focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.tax_code" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Citta</label>
                    <input v-model="form.city" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.city" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Indirizzo</label>
                    <input v-model="form.address" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="4" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600"></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Link :href="isEdit ? route('clients.show', client.id) : route('clients.index')" class="rounded border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Annulla
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-60">
                    Salva
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
