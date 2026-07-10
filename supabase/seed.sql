-- Demo seed for Supabase.
-- 1. Create a user from Supabase Auth first.
-- 2. Replace the UUID below with that user's id from auth.users.
-- 3. Run in staging first, then repeat in production only if demo data is desired.

select set_config('app.seed_user_id', '00000000-0000-0000-0000-000000000000', false);

insert into public.profiles (id, name, email, role)
values (
    current_setting('app.seed_user_id')::uuid,
    'Admin Gestionale',
    'admin@example.com',
    'admin'
)
on conflict (id) do update set
    name = excluded.name,
    email = excluded.email,
    role = excluded.role;

with company_rows(name, contact_name, contact_phone, contact_email) as (
    values
        ('Reale Mutua', 'Giulia Ferri', '+39 02 1111111', 'reale.mutua@demo.test'),
        ('Allianz', 'Marco Conti', '+39 02 2222222', 'allianz@demo.test'),
        ('Generali Italia', 'Elena Riva', '+39 02 3333333', 'generali.italia@demo.test'),
        ('UnipolSai', 'Davide Moretti', '+39 02 4444444', 'unipolsai@demo.test'),
        ('Zurich Italia', 'Laura Galli', '+39 02 5555555', 'zurich.italia@demo.test')
)
insert into public.insurance_companies (user_id, name, contact_name, contact_phone, contact_email, notes)
select current_setting('app.seed_user_id')::uuid, name, contact_name, contact_phone, contact_email, 'Compagnia demo'
from company_rows
on conflict (user_id, name) do nothing;

with client_rows(first_name, last_name, phone, email, tax_code, address, city) as (
    values
        ('Mario', 'Rossi', '+39 333 111 1111', 'mario.rossi@example.com', 'RSSMRA80A01H501U', 'Via Roma 1', 'Milano'),
        ('Lucia', 'Bianchi', '+39 333 222 2222', 'lucia.bianchi@example.com', 'BNCLCU82B41F205X', 'Via Verdi 12', 'Torino'),
        ('Paolo', 'Verdi', '+39 333 333 3333', 'paolo.verdi@example.com', 'VRDPLA75C10D612Z', 'Corso Italia 20', 'Bologna'),
        ('Sara', 'Neri', '+39 333 444 4444', 'sara.neri@example.com', 'NRESRA90D55L219Q', 'Via Milano 7', 'Firenze'),
        ('Andrea', 'Gallo', '+39 333 555 5555', 'andrea.gallo@example.com', 'GLLNDR88E12H703R', 'Piazza Dante 3', 'Roma')
)
insert into public.clients (user_id, first_name, last_name, phone, email, tax_code, address, city, notes)
select current_setting('app.seed_user_id')::uuid, first_name, last_name, phone, email, tax_code, address, city, 'Cliente demo'
from client_rows
on conflict (user_id, tax_code) do nothing;

insert into public.policies (
    user_id,
    client_id,
    insurance_company_id,
    type,
    number,
    start_date,
    end_date,
    annual_premium,
    status,
    notes
)
select
    current_setting('app.seed_user_id')::uuid,
    c.id,
    ic.id,
    policy_data.type,
    policy_data.number,
    policy_data.start_date,
    policy_data.end_date,
    policy_data.annual_premium,
    policy_data.status::policy_status,
    policy_data.notes
from (
    values
        ('RSSMRA80A01H501U', 'Reale Mutua', 'Auto', 'POL-DEMO-0001', current_date - interval '380 days', current_date - interval '20 days', 520.00, 'scaduta', 'Polizza scaduta: idonea a preventivo recupero.'),
        ('BNCLCU82B41F205X', 'Allianz', 'Casa', 'POL-DEMO-0002', current_date - interval '360 days', current_date + interval '5 days', 310.00, 'in_scadenza', 'In scadenza entro 7 giorni.'),
        ('VRDPLA75C10D612Z', 'Generali Italia', 'Moto', 'POL-DEMO-0003', current_date - interval '300 days', current_date + interval '25 days', 250.00, 'in_scadenza', 'In scadenza entro 30 giorni.'),
        ('NRESRA90D55L219Q', 'UnipolSai', 'Vita', 'POL-DEMO-0004', current_date - interval '120 days', current_date + interval '240 days', 980.00, 'attiva', 'Polizza attiva.'),
        ('GLLNDR88E12H703R', 'Zurich Italia', 'Infortuni', 'POL-DEMO-0005', current_date - interval '500 days', current_date - interval '60 days', 180.00, 'rinnovata', 'Polizza gia rinnovata.')
) as policy_data(tax_code, company_name, type, number, start_date, end_date, annual_premium, status, notes)
join public.clients c
    on c.tax_code = policy_data.tax_code
    and c.user_id = current_setting('app.seed_user_id')::uuid
join public.insurance_companies ic
    on ic.name = policy_data.company_name
    and ic.user_id = current_setting('app.seed_user_id')::uuid
on conflict (user_id, number) do nothing;
