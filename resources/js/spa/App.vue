<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { supabase } from './lib/supabase.js';

const router = useRouter();
const route = useRoute();
const session = ref(null);
const mobileOpen = ref(false);
const appEnv = import.meta.env.VITE_APP_ENV || 'staging';

const navigation = [
    { label: 'Dashboard', to: '/', icon: 'DB' },
    { label: 'Clienti', to: '/clients', icon: 'CL' },
    { label: 'Compagnie', to: '/companies', icon: 'CO' },
    { label: 'Polizze', to: '/policies', icon: 'PL' },
    { label: 'Scadenziario', to: '/expirations', icon: 'SC' },
    { label: 'Preventivi', to: '/quotes', icon: 'PR' },
];

onMounted(async () => {
    const { data } = await supabase.auth.getSession();
    session.value = data.session;

    supabase.auth.onAuthStateChange((_event, currentSession) => {
        session.value = currentSession;
    });
});

const isActive = (to) => (to === '/' ? route.path === '/' : route.path.startsWith(to));

const signOut = async () => {
    await supabase.auth.signOut();
    await router.push('/login');
};
</script>

<template>
    <RouterView v-if="route.meta.guest" />

    <div v-else class="modern-shell min-h-screen bg-[#f5f7fb] text-slate-900">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 border-r border-slate-200 bg-slate-950 px-4 py-5 text-white lg:block">
            <RouterLink to="/" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded bg-blue-600 text-sm font-bold">GA</span>
                <span>
                    <span class="block text-sm font-semibold">Gestionale</span>
                    <span class="block text-xs text-slate-400">Assicurazioni</span>
                </span>
            </RouterLink>

            <nav class="mt-8 space-y-1">
                <RouterLink
                    v-for="item in navigation"
                    :key="item.to"
                    :to="item.to"
                    :class="[
                        'flex items-center gap-3 rounded px-3 py-2.5 text-sm font-medium transition',
                        isActive(item.to) ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-300 hover:bg-slate-900 hover:text-white',
                    ]"
                >
                    <span :class="['grid h-8 w-8 place-items-center rounded text-xs font-bold', isActive(item.to) ? 'bg-blue-50 text-blue-700' : 'bg-slate-900 text-slate-400']">
                        {{ item.icon }}
                    </span>
                    {{ item.label }}
                </RouterLink>
            </nav>

            <div class="absolute bottom-5 left-4 right-4 rounded border border-slate-800 bg-slate-900 p-3">
                <p class="truncate text-sm font-medium">{{ session?.user?.email ?? 'Utente' }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ appEnv }}</p>
            </div>
        </aside>

        <div class="lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
                <div class="flex min-h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="grid h-10 w-10 place-items-center rounded border border-slate-300 text-slate-700 lg:hidden"
                            aria-label="Apri menu"
                            @click="mobileOpen = true"
                        >
                            ☰
                        </button>
                        <div>
                            <p class="text-xs font-semibold uppercase text-blue-700">CRM assicurativo</p>
                            <h1 class="text-lg font-semibold text-slate-950">{{ navigation.find((item) => isActive(item.to))?.label ?? 'Gestionale Assicurazioni' }}</h1>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="min-h-10 rounded border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                        @click="signOut"
                    >
                        Esci
                    </button>
                </div>
            </header>

            <div v-if="mobileOpen" class="fixed inset-0 z-50 lg:hidden">
                <button class="absolute inset-0 bg-slate-950/50" aria-label="Chiudi menu" @click="mobileOpen = false"></button>
                <aside class="relative h-full w-80 max-w-[85vw] bg-slate-950 px-4 py-5 text-white shadow-2xl">
                    <div class="flex items-center justify-between">
                        <RouterLink to="/" class="flex items-center gap-3" @click="mobileOpen = false">
                            <span class="grid h-10 w-10 place-items-center rounded bg-blue-600 text-sm font-bold">GA</span>
                            <span class="text-sm font-semibold">Gestionale Assicurazioni</span>
                        </RouterLink>
                        <button class="grid h-10 w-10 place-items-center rounded bg-slate-900 text-slate-300" aria-label="Chiudi menu" @click="mobileOpen = false">×</button>
                    </div>
                    <nav class="mt-8 space-y-1">
                        <RouterLink
                            v-for="item in navigation"
                            :key="item.to"
                            :to="item.to"
                            :class="[
                                'flex items-center gap-3 rounded px-3 py-3 text-sm font-medium',
                                isActive(item.to) ? 'bg-white text-slate-950' : 'text-slate-300',
                            ]"
                            @click="mobileOpen = false"
                        >
                            <span class="grid h-8 w-8 place-items-center rounded bg-slate-900 text-xs font-bold text-slate-400">{{ item.icon }}</span>
                            {{ item.label }}
                        </RouterLink>
                    </nav>
                </aside>
            </div>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <RouterView />
            </main>
        </div>
    </div>
</template>
