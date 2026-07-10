<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    navigation: {
        type: Array,
        required: true,
    },
    mobileOpen: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['toggle-mobile']);

const page = usePage();
</script>

<template>
    <nav class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
        <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <button
                type="button"
                @click="$emit('toggle-mobile')"
                class="inline-flex h-11 w-11 items-center justify-center rounded border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 lg:hidden"
                aria-label="Apri menu"
            >
                <span v-if="mobileOpen" class="text-xl leading-none">x</span>
                <span v-else class="flex flex-col gap-1">
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                </span>
            </button>

            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-950">
                    <slot name="title">Gestionale Assicurazioni</slot>
                </p>
                <p class="hidden text-xs text-slate-500 sm:block">Area operativa</p>
            </div>

            <div class="flex items-center gap-3">
                <div v-if="page.props.flash?.success" class="hidden rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 md:block">
                    {{ page.props.flash.success }}
                </div>

                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button
                            type="button"
                            class="inline-flex min-h-10 items-center rounded border border-slate-200 bg-white px-2 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:px-3"
                        >
                            <span class="mr-2 grid h-7 w-7 place-items-center rounded bg-blue-50 text-xs font-bold text-blue-700">
                                {{ page.props.auth.user.name.slice(0, 1) }}
                            </span>
                            <span class="hidden sm:inline">{{ page.props.auth.user.name }}</span>
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

        <div v-show="mobileOpen" class="border-t border-slate-200 bg-white lg:hidden">
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
</template>
