<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);

const navigation = [
    { label: 'Dashboard', routeName: 'dashboard', match: 'dashboard' },
    { label: 'Clienti', routeName: 'clients.index', match: 'clients.*' },
    { label: 'Compagnie', routeName: 'insurance-companies.index', match: 'insurance-companies.*' },
    { label: 'Polizze', routeName: 'policies.index', match: 'policies.*' },
    { label: 'Scadenziario', routeName: 'expirations.index', match: 'expirations.*' },
];
</script>

<template>
    <div class="min-h-screen bg-slate-100 text-slate-900">
        <aside class="fixed inset-y-0 left-0 z-20 hidden w-72 border-r border-slate-200 bg-white lg:block">
            <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-6">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded bg-emerald-700 text-sm font-bold text-white">GA</span>
                    <span>
                        <span class="block text-sm font-semibold">Gestionale</span>
                        <span class="block text-xs text-slate-500">Assicurazioni</span>
                    </span>
                </Link>
            </div>

            <nav class="space-y-1 px-4 py-5">
                <Link
                    v-for="item in navigation"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    :class="[
                        'flex items-center rounded px-3 py-2 text-sm font-medium transition',
                        route().current(item.match)
                            ? 'bg-emerald-50 text-emerald-800'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950',
                    ]"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <div class="lg:pl-72">
            <nav class="sticky top-0 z-10 border-b border-slate-200 bg-white">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex h-10 w-10 items-center justify-center rounded border border-slate-200 text-slate-600 lg:hidden"
                    >
                        <span class="sr-only">Menu</span>
                        <span class="text-xl leading-none">{{ showingNavigationDropdown ? 'x' : '=' }}</span>
                    </button>

                    <div class="hidden min-w-0 lg:block">
                        <p class="text-sm font-semibold text-slate-950">
                            <slot name="title">Gestionale Assicurazioni</slot>
                        </p>
                        <p class="text-xs text-slate-500">Area protetta</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div v-if="$page.props.flash?.success" class="hidden rounded bg-emerald-50 px-3 py-2 text-sm text-emerald-800 md:block">
                            {{ $page.props.flash.success }}
                        </div>

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                >
                                    {{ $page.props.auth.user.name }}
                                    <span class="ms-2 text-slate-400">v</span>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profilo
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Esci
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <div v-show="showingNavigationDropdown" class="border-t border-slate-200 bg-white lg:hidden">
                    <div class="space-y-1 px-2 py-3">
                        <ResponsiveNavLink
                            v-for="item in navigation"
                            :key="item.routeName"
                            :href="route(item.routeName)"
                            :active="route().current(item.match)"
                        >
                            {{ item.label }}
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <header v-if="$slots.header" class="border-b border-slate-200 bg-white">
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
