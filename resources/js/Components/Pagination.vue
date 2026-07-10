<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="flex flex-wrap gap-2">
        <component
            :is="link.url ? Link : 'span'"
            v-for="link in links"
            :key="link.label"
            :href="link.url"
            preserve-scroll
            :class="[
                'rounded border px-3 py-2 text-sm',
                link.active
                    ? 'border-blue-600 bg-blue-600 text-white'
                    : link.url
                      ? 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                      : 'border-slate-100 bg-slate-100 text-slate-400',
            ]"
            v-html="link.label"
        />
    </nav>
</template>
