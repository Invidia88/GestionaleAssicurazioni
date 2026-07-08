<script setup>
const props = defineProps({
    status: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});

const classes = {
    attiva: 'border-emerald-200 bg-emerald-50 text-emerald-800',
    in_scadenza: 'border-amber-200 bg-amber-50 text-amber-800',
    scaduta: 'border-red-200 bg-red-50 text-red-800',
    rinnovata: 'border-blue-200 bg-blue-50 text-blue-800',
};
</script>

<template>
    <span :class="['inline-flex rounded border px-2 py-1 text-xs font-semibold', classes[props.status] ?? 'border-slate-200 bg-slate-50 text-slate-700']">
        {{ props.label }}
    </span>
</template>
