<template>
    <div class="flex min-h-screen items-center justify-center bg-[#f5f7fb] px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded bg-slate-950 text-base font-bold text-white shadow-sm">
                    GA
                </div>
                <h1 class="mt-4 text-2xl font-semibold text-slate-950">Gestionale Assicurazioni</h1>
                <p class="mt-1 text-sm text-slate-500">Accedi all'area operativa</p>
            </div>

            <div class="overflow-hidden rounded border border-slate-200 bg-white px-6 py-6 shadow-xl shadow-slate-200/70">
                <slot />
            </div>
        </div>
    </div>
</template>
