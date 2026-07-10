<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [Number, String],
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
    tone: {
        type: String,
        default: 'blue',
    },
    helper: {
        type: String,
        default: '',
    },
});

const tones = {
    blue: 'bg-blue-50 text-blue-700 border-blue-100',
    green: 'bg-emerald-50 text-emerald-700 border-emerald-100',
    amber: 'bg-amber-50 text-amber-700 border-amber-100',
    orange: 'bg-orange-50 text-orange-700 border-orange-100',
    red: 'bg-red-50 text-red-700 border-red-100',
    slate: 'bg-slate-100 text-slate-700 border-slate-200',
};
</script>

<template>
    <article class="rounded border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/70">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">{{ label }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ value }}</p>
            </div>
            <span :class="['grid h-11 w-11 shrink-0 place-items-center rounded border text-xs font-bold', tones[tone] ?? tones.blue]">
                {{ icon }}
            </span>
        </div>
        <p v-if="helper" class="mt-3 text-sm text-slate-500">{{ helper }}</p>
    </article>
</template>
