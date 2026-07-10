<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import DataTableWrapper from '@/Components/DataTableWrapper.vue';
import EmptyState from '@/Components/EmptyState.vue';
import FormCard from '@/Components/FormCard.vue';
import { deleteRecord, loadClients, saveRecord } from '../lib/api.js';
import { fullName } from '../lib/domain.js';

const clients = ref([]);
const search = ref('');
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const editingId = ref(null);
const showForm = ref(false);

const form = reactive({
    first_name: '',
    last_name: '',
    phone: '',
    email: '',
    tax_code: '',
    address: '',
    city: '',
    notes: '',
});

const resetForm = () => {
    editingId.value = null;
    showForm.value = false;
    Object.assign(form, {
        first_name: '',
        last_name: '',
        phone: '',
        email: '',
        tax_code: '',
        address: '',
        city: '',
        notes: '',
    });
};

const openCreateForm = () => {
    resetForm();
    showForm.value = true;
};

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        clients.value = await loadClients();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        loading.value = false;
    }
};

onMounted(load);

const filteredClients = computed(() => {
    const term = search.value.toLowerCase().trim();

    if (!term) {
        return clients.value;
    }

    return clients.value.filter((client) =>
        [fullName(client), client.phone, client.email, client.tax_code, client.city]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

const edit = (client) => {
    editingId.value = client.id;
    showForm.value = true;
    Object.assign(form, {
        first_name: client.first_name ?? '',
        last_name: client.last_name ?? '',
        phone: client.phone ?? '',
        email: client.email ?? '',
        tax_code: client.tax_code ?? '',
        address: client.address ?? '',
        city: client.city ?? '',
        notes: client.notes ?? '',
    });
};

const submit = async () => {
    saving.value = true;
    error.value = '';

    try {
        await saveRecord('clients', form, editingId.value);
        await load();
        resetForm();
    } catch (exception) {
        error.value = exception.message;
    } finally {
        saving.value = false;
    }
};

const remove = async (client) => {
    if (!confirm(`Eliminare ${fullName(client)}? Verranno eliminate anche le polizze collegate.`)) {
        return;
    }

    try {
        await deleteRecord('clients', client.id);
        await load();
    } catch (exception) {
        error.value = exception.message;
    }
};
</script>

<template>
    <div class="space-y-6">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-blue-700">Anagrafiche</p>
                <h2 class="mt-1 text-3xl font-semibold text-slate-950">Clienti</h2>
                <p class="mt-1 text-sm text-slate-500">Gestione clienti su Supabase.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <input v-model="search" type="search" placeholder="Cerca cliente" class="min-h-10 rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="button" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" @click="openCreateForm">
                    Crea nuovo cliente
                </button>
            </div>
        </header>

        <p v-if="error" class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</p>

        <FormCard v-if="showForm" :title="editingId ? 'Modifica cliente' : 'Nuovo cliente'" description="Nome e cognome sono obbligatori. Gli altri dati aiutano reminder e ricerche.">
            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                <div>
                    <label class="text-sm font-medium text-slate-700">Nome</label>
                    <input v-model="form.first_name" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Cognome</label>
                    <input v-model="form.last_name" required class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Telefono</label>
                    <input v-model="form.phone" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Email</label>
                    <input v-model="form.email" type="email" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Codice fiscale</label>
                    <input v-model="form.tax_code" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm uppercase focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Citta</label>
                    <input v-model="form.city" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Indirizzo</label>
                    <input v-model="form.address" class="mt-1 min-h-10 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Note</label>
                    <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row md:col-span-2">
                    <button type="submit" :disabled="saving" class="min-h-10 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
                        {{ saving ? 'Salvataggio...' : editingId ? 'Salva modifiche' : 'Crea cliente' }}
                    </button>
                    <button type="button" class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="resetForm">
                        Annulla
                    </button>
                </div>
            </form>
        </FormCard>

        <DataTableWrapper class="hidden lg:block">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Contatti</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Citta</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr v-for="client in filteredClients" :key="client.id">
                        <td class="px-4 py-3 text-sm">
                            <p class="font-semibold text-slate-950">{{ fullName(client) }}</p>
                            <p class="text-slate-500">{{ client.tax_code || '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            <p>{{ client.phone || '-' }}</p>
                            <p>{{ client.email || '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ client.city || '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button class="rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="edit(client)">Modifica</button>
                            <button class="ml-2 rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" @click="remove(client)">Elimina</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTableWrapper>

        <div class="space-y-3 lg:hidden">
            <article v-for="client in filteredClients" :key="client.id" class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <p class="font-semibold text-slate-950">{{ fullName(client) }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ client.phone || 'Telefono non indicato' }}</p>
                <p class="text-sm text-slate-500">{{ client.email || 'Email non indicata' }}</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <button class="min-h-10 rounded border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700" @click="edit(client)">Modifica</button>
                    <button class="min-h-10 rounded border border-red-200 px-3 py-2 text-sm font-semibold text-red-700" @click="remove(client)">Elimina</button>
                </div>
            </article>
        </div>

        <EmptyState v-if="!loading && filteredClients.length === 0" title="Nessun cliente" description="Crea una nuova anagrafica o cambia ricerca." />
    </div>
</template>
