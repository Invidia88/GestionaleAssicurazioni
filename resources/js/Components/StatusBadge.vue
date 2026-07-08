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
    attiva: 'bg-emerald-100 text-emerald-800',
    in_scadenza: 'bg-amber-100 text-amber-800',
    scaduta: 'bg-red-100 text-red-800',
    rinnovata: 'bg-blue-100 text-blue-800',
};
</script>

<template>
    <span :class="['inline-flex rounded px-2 py-1 text-xs font-semibold', classes[props.status] ?? 'bg-slate-100 text-slate-700']">
        {{ props.label }}
    </span>
</template>
