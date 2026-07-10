<script setup>
import { Link, usePage } from '@inertiajs/vue3';

defineProps({
    navigation: {
        type: Array,
        required: true,
    },
});

const page = usePage();
</script>

<template>
    <aside class="fixed inset-y-0 left-0 z-20 hidden w-72 border-r border-slate-800 bg-slate-950 lg:block">
        <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
            <Link :href="route('dashboard')" class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded bg-blue-600 text-sm font-bold text-white shadow-sm">GA</span>
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
                            ? 'bg-blue-600 text-white'
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
                <p class="mt-1 truncate text-sm font-medium text-white">{{ page.props.auth.user.email }}</p>
            </div>
        </div>
    </aside>
</template>
