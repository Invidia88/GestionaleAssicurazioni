import { createRouter, createWebHistory } from 'vue-router';
import { hasSupabaseConfig, supabase } from './lib/supabase.js';
import LoginPage from './views/LoginPage.vue';
import DashboardPage from './views/DashboardPage.vue';
import ClientsPage from './views/ClientsPage.vue';
import CompaniesPage from './views/CompaniesPage.vue';
import PoliciesPage from './views/PoliciesPage.vue';
import ExpirationsPage from './views/ExpirationsPage.vue';
import QuotesPage from './views/QuotesPage.vue';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', name: 'login', component: LoginPage, meta: { guest: true } },
        { path: '/', name: 'dashboard', component: DashboardPage },
        { path: '/clients', name: 'clients', component: ClientsPage },
        { path: '/companies', name: 'companies', component: CompaniesPage },
        { path: '/policies', name: 'policies', component: PoliciesPage },
        { path: '/expirations', name: 'expirations', component: ExpirationsPage },
        { path: '/quotes', name: 'quotes', component: QuotesPage },
        { path: '/:pathMatch(.*)*', redirect: '/' },
    ],
});

router.beforeEach(async (to) => {
    if (!hasSupabaseConfig) {
        return true;
    }

    const { data } = await supabase.auth.getSession();

    if (!data.session && !to.meta.guest) {
        return { name: 'login' };
    }

    if (data.session && to.meta.guest) {
        return { name: 'dashboard' };
    }

    return true;
});
