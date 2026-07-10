<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <template v-if="$slots.title" #title>
            <slot name="title" />
        </template>

        <template v-if="$slots.header" #header>
            <slot name="header" />
        </template>

        <slot />
    </AuthenticatedLayout>
</template>
