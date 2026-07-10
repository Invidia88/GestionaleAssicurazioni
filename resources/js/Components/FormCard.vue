<script setup>
defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="max-w-5xl rounded border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
        <div v-if="title || description" class="border-b border-slate-200 px-5 py-4">
            <h2 v-if="title" class="text-base font-semibold text-slate-950">{{ title }}</h2>
            <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
        </div>
        <div class="p-5">
            <slot />
        </div>
    </div>
</template>
