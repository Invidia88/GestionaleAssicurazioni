<script setup>
defineProps({
    href: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <a
        v-if="href"
        :href="href"
        target="_blank"
        rel="noreferrer"
        class="inline-flex min-h-10 items-center justify-center rounded border border-transparent bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
    >
        WhatsApp
    </a>
</template>
