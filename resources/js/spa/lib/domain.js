export const policyTypes = ['Auto', 'Moto', 'Casa', 'Vita', 'Infortuni', 'Aziendale', 'Altro'];

export const policyStatuses = [
    { value: 'attiva', label: 'Attiva' },
    { value: 'in_scadenza', label: 'In scadenza' },
    { value: 'scaduta', label: 'Scaduta' },
    { value: 'rinnovata', label: 'Rinnovata' },
];

export const quoteStatuses = [
    { value: 'bozza', label: 'Bozza' },
    { value: 'inviato', label: 'Inviato' },
    { value: 'accettato', label: 'Accettato' },
    { value: 'rifiutato', label: 'Rifiutato' },
    { value: 'scaduto', label: 'Scaduto' },
];

export const statusLabel = (status) =>
    policyStatuses.find((item) => item.value === status)?.label ?? status;

export const quoteStatusLabel = (status) =>
    quoteStatuses.find((item) => item.value === status)?.label ?? status;

export const formatDate = (date) => {
    if (!date) {
        return '-';
    }

    return new Intl.DateTimeFormat('it-IT').format(new Date(`${date}T00:00:00`));
};

export const formatCurrency = (value) =>
    new Intl.NumberFormat('it-IT', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(value ?? 0));

export const fullName = (client) =>
    [client?.first_name, client?.last_name].filter(Boolean).join(' ') || '-';

export const todayIso = () => new Date().toISOString().slice(0, 10);

export const addDaysIso = (date, days) => {
    const value = new Date(`${date}T00:00:00`);
    value.setDate(value.getDate() + days);
    return value.toISOString().slice(0, 10);
};

export const isRecoveryQuoteAvailable = (policy) => {
    if (!policy?.end_date || policy.status === 'rinnovata') {
        return false;
    }

    return addDaysIso(policy.end_date, 15) <= todayIso();
};

export const whatsappUrl = (policy) => {
    const phone = policy?.client?.phone?.replace(/[^\d+]/g, '');

    if (!phone) {
        return null;
    }

    const message = `Ciao ${fullName(policy.client)}, ti ricordiamo che la tua polizza ${policy.type} con compagnia ${policy.insurance_company?.name ?? ''} scade/scadeva il ${formatDate(policy.end_date)}. Contattaci per valutare il rinnovo.`;

    return `https://wa.me/${phone.replace(/^\+/, '')}?text=${encodeURIComponent(message)}`;
};

export const emptyPolicyForm = () => ({
    client_id: '',
    insurance_company_id: '',
    type: 'Auto',
    number: '',
    start_date: todayIso(),
    end_date: addDaysIso(todayIso(), 365),
    annual_premium: '',
    status: 'attiva',
    notes: '',
});
