<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';

const showingNavigationDropdown = ref(false);

const navigation = [
    { label: 'Dashboard', routeName: 'dashboard', match: 'dashboard', icon: 'DB' },
    { label: 'Clienti', routeName: 'clients.index', match: 'clients.*', icon: 'CL' },
    { label: 'Compagnie', routeName: 'insurance-companies.index', match: 'insurance-companies.*', icon: 'CO' },
    { label: 'Polizze', routeName: 'policies.index', match: 'policies.*', icon: 'PL' },
    { label: 'Scadenziario', routeName: 'expirations.index', match: 'expirations.*', icon: 'SC' },
];
</script>

<template>
    <div class="modern-shell min-h-screen bg-[#f5f7fb] text-slate-900">
        <Sidebar :navigation="navigation" />

        <div class="lg:pl-72">
            <Topbar
                :navigation="navigation"
                :mobile-open="showingNavigationDropdown"
                @toggle-mobile="showingNavigationDropdown = !showingNavigationDropdown"
            >
                <template #title>
                    <slot name="title">Gestionale Assicurazioni</slot>
                </template>
            </Topbar>

            <header v-if="$slots.header" class="border-b border-slate-200 bg-[#f5f7fb]">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 md:hidden">
                    {{ $page.props.flash.success }}
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
