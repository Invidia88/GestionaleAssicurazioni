<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="rounded border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center">
        <div class="mx-auto grid h-12 w-12 place-items-center rounded bg-white text-sm font-bold text-slate-500 shadow-sm">GA</div>
        <h3 class="mt-4 text-base font-semibold text-slate-950">{{ title }}</h3>
        <p v-if="description" class="mx-auto mt-1 max-w-md text-sm text-slate-500">{{ description }}</p>
    </div>
</template>
