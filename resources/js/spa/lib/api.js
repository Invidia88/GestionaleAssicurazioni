import { supabase } from './supabase.js';

export const currentUserId = async () => {
    const { data, error } = await supabase.auth.getUser();

    if (error || !data.user) {
        throw new Error('Utente non autenticato.');
    }

    return data.user.id;
};

export const loadClients = async () => {
    const { data, error } = await supabase
        .from('clients')
        .select('*')
        .order('last_name', { ascending: true })
        .order('first_name', { ascending: true });

    if (error) {
        throw error;
    }

    return data ?? [];
};

export const loadCompanies = async () => {
    const { data, error } = await supabase
        .from('insurance_companies')
        .select('*')
        .order('name', { ascending: true });

    if (error) {
        throw error;
    }

    return data ?? [];
};

export const loadPolicies = async () => {
    const { data, error } = await supabase
        .from('policies')
        .select(`
            *,
            client:clients(*),
            insurance_company:insurance_companies(*)
        `)
        .order('end_date', { ascending: true });

    if (error) {
        throw error;
    }

    return data ?? [];
};

export const loadQuotes = async () => {
    const { data, error } = await supabase
        .from('quotes')
        .select(`
            *,
            client:clients(*),
            insurance_company:insurance_companies(*),
            source_policy:policies(*)
        `)
        .order('created_at', { ascending: false });

    if (error) {
        throw error;
    }

    return data ?? [];
};

export const saveRecord = async (table, form, id = null) => {
    const payload = { ...form };
    const userId = await currentUserId();

    Object.keys(payload).forEach((key) => {
        if (payload[key] === '') {
            payload[key] = null;
        }
    });

    if (id) {
        const { error } = await supabase.from(table).update(payload).eq('id', id);

        if (error) {
            throw error;
        }

        return;
    }

    const { error } = await supabase.from(table).insert({
        ...payload,
        user_id: userId,
    });

    if (error) {
        throw error;
    }
};

export const deleteRecord = async (table, id) => {
    const { error } = await supabase.from(table).delete().eq('id', id);

    if (error) {
        throw error;
    }
};

export const createQuoteFromPolicy = async (policy, companyId) => {
    const userId = await currentUserId();
    const number = `PREV-${policy.number}-${new Date().toISOString().slice(0, 10)}`;

    const { error } = await supabase.from('quotes').insert({
        user_id: userId,
        source_policy_id: policy.id,
        client_id: policy.client_id,
        insurance_company_id: companyId,
        type: policy.type,
        number,
        start_date: null,
        end_date: null,
        annual_premium: null,
        status: 'bozza',
        notes: `Preventivo recupero creato dalla polizza ${policy.number}.`,
    });

    if (error) {
        throw error;
    }
};
