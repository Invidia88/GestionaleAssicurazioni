<script setup>
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    company: Object,
});

const isEdit = Boolean(props.company);
const form = useForm({
    name: props.company?.name ?? '',
    contact_name: props.company?.contact_name ?? '',
    contact_phone: props.company?.contact_phone ?? '',
    contact_email: props.company?.contact_email ?? '',
    notes: props.company?.notes ?? '',
});

const submit = () => {
    if (isEdit) {
        form.put(route('insurance-companies.update', props.company.id));
        return;
    }

    form.post(route('insurance-companies.store'));
};
</script>

<template>
    <Head :title="isEdit ? 'Modifica compagnia' : 'Nuova compagnia'" />

    <AuthenticatedLayout>
        <template #title>{{ isEdit ? 'Modifica compagnia' : 'Nuova compagnia' }}</template>

        <template #header>
            <div>
                <h1 class="text-2xl font-semibold text-slate-950">{{ isEdit ? 'Modifica compagnia' : 'Nuova compagnia' }}</h1>
                <p class="mt-1 text-sm text-slate-500">Mantieni aggiornati i riferimenti della compagnia.</p>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl rounded border border-slate-200 bg-white p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Nome compagnia</label>
                    <input v-model="form.name" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Referente</label>
                    <input v-model="form.contact_name" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.contact_name" class="mt-2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Telefono referente</label>
                    <input v-model="form.contact_phone" type="text" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.contact_phone" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Email referente</label>
                    <input v-model="form.contact_email" type="email" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    <InputError :message="form.errors.contact_email" class="mt-2" />
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="4" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600"></textarea>
                    <InputError :message="form.errors.notes" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <Link :href="isEdit ? route('insurance-companies.show', company.id) : route('insurance-companies.index')" class="rounded border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Annulla
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 disabled:opacity-60">
                    Salva
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
