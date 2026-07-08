<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

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
        <aside class="fixed inset-y-0 left-0 z-20 hidden w-72 border-r border-slate-800 bg-slate-950 lg:block">
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded bg-white text-sm font-bold text-slate-950 shadow-sm">GA</span>
                    <span>
                        <span class="block text-sm font-semibold text-white">Gestionale</span>
                        <span class="block text-xs text-slate-400">Assicurazioni</span>
                    </span>
                </Link>
            </div>

            <nav class="space-y-2 px-4 py-6">
                <Link
                    v-for="item in navigation"
                    :key="item.routeName"
                    :href="route(item.routeName)"
                    :class="[
                        'group flex items-center gap-3 rounded px-3 py-3 text-sm font-medium transition',
                        route().current(item.match)
                            ? 'bg-white text-slate-950 shadow-sm'
                            : 'text-slate-300 hover:bg-white/10 hover:text-white',
                    ]"
                >
                    <span
                        :class="[
                            'grid h-8 w-8 shrink-0 place-items-center rounded text-[11px] font-bold',
                            route().current(item.match)
                                ? 'bg-emerald-700 text-white'
                                : 'bg-white/10 text-slate-300 group-hover:bg-white/15 group-hover:text-white',
                        ]"
                    >
                        {{ item.icon }}
                    </span>
                    {{ item.label }}
                </Link>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 border-t border-white/10 p-4">
                <div class="rounded border border-white/10 bg-white/5 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Accesso</p>
                    <p class="mt-1 truncate text-sm font-medium text-white">{{ $page.props.auth.user.email }}</p>
                </div>
            </div>
        </aside>

        <div class="lg:pl-72">
            <nav class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex h-10 w-10 items-center justify-center rounded border border-slate-200 bg-white text-slate-700 shadow-sm lg:hidden"
                    >
                        <span class="sr-only">Menu</span>
                        <span v-if="showingNavigationDropdown" class="text-xl leading-none">x</span>
                        <span v-else class="flex flex-col gap-1">
                            <span class="block h-0.5 w-5 rounded bg-current"></span>
                            <span class="block h-0.5 w-5 rounded bg-current"></span>
                            <span class="block h-0.5 w-5 rounded bg-current"></span>
                        </span>
                    </button>

                    <div class="hidden min-w-0 lg:block">
                        <p class="text-sm font-semibold text-slate-950">
                            <slot name="title">Gestionale Assicurazioni</slot>
                        </p>
                        <p class="text-xs text-slate-500">Area operativa</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div v-if="$page.props.flash?.success" class="hidden rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 md:block">
                            {{ $page.props.flash.success }}
                        </div>

                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:border-slate-300 hover:bg-slate-50"
                                >
                                    <span class="mr-2 grid h-7 w-7 place-items-center rounded bg-slate-100 text-xs font-bold text-slate-700">
                                        {{ $page.props.auth.user.name.slice(0, 1) }}
                                    </span>
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
