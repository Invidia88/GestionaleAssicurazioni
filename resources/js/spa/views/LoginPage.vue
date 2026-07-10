<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { supabase } from '../lib/supabase.js';

const router = useRouter();
const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const debugMessage = ref('');

const friendlyAuthError = (authError) => {
    const message = authError?.message ?? '';

    if (message.toLowerCase().includes('email not confirmed')) {
        return 'Email non confermata. In Supabase vai in Authentication > Users, apri l’utente e conferma l’email oppure abilita Auto Confirm per lo staging.';
    }

    if (message.toLowerCase().includes('invalid login credentials')) {
        return 'Email o password non corrette. Verifica di usare l’utente creato nello stesso progetto Supabase collegato a questa app.';
    }

    return message || 'Accesso non riuscito. Controlla utente, password e configurazione Supabase.';
};

const submit = async () => {
    loading.value = true;
    error.value = '';
    debugMessage.value = '';

    const { data, error: authError } = await supabase.auth.signInWithPassword({
        email: email.value.trim().toLowerCase(),
        password: password.value,
    });

    loading.value = false;

    if (authError) {
        error.value = friendlyAuthError(authError);
        debugMessage.value = authError.message;
        return;
    }

    if (!data.session) {
        error.value = 'Login completato senza sessione valida. Controlla le impostazioni Auth del progetto Supabase.';
        return;
    }

    await router.replace('/');
};
</script>

<template>
    <main class="grid min-h-screen place-items-center bg-[#f5f7fb] px-4 py-10">
        <section class="w-full max-w-md rounded border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/70">
            <div class="mb-6">
                <div class="grid h-12 w-12 place-items-center rounded bg-blue-600 text-sm font-bold text-white">GA</div>
                <h1 class="mt-5 text-2xl font-semibold text-slate-950">Gestionale Assicurazioni</h1>
                <p class="mt-1 text-sm text-slate-500">Accedi con l'utente creato in Supabase Auth.</p>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label class="text-sm font-medium text-slate-700">Email</label>
                    <input v-model="email" required type="email" class="mt-1 min-h-11 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Password</label>
                    <input v-model="password" required type="password" class="mt-1 min-h-11 w-full rounded border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div v-if="error" class="rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    <p>{{ error }}</p>
                    <p v-if="debugMessage" class="mt-1 text-xs text-red-600">Dettaglio Supabase: {{ debugMessage }}</p>
                </div>
                <button type="submit" :disabled="loading" class="min-h-11 w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ loading ? 'Accesso...' : 'Accedi' }}
                </button>
            </form>
        </section>
    </main>
</template>
