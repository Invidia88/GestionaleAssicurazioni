-- Row Level Security policies for Supabase.
-- All business data is scoped to auth.uid().

alter table public.profiles enable row level security;
alter table public.clients enable row level security;
alter table public.insurance_companies enable row level security;
alter table public.policies enable row level security;
alter table public.quotes enable row level security;

drop policy if exists "profiles_select_own" on public.profiles;
create policy "profiles_select_own"
on public.profiles
for select
using (id = auth.uid());

drop policy if exists "profiles_update_own" on public.profiles;
create policy "profiles_update_own"
on public.profiles
for update
using (id = auth.uid())
with check (id = auth.uid());

drop policy if exists "clients_crud_own" on public.clients;
create policy "clients_crud_own"
on public.clients
for all
using (user_id = auth.uid())
with check (user_id = auth.uid());

drop policy if exists "companies_crud_own" on public.insurance_companies;
create policy "companies_crud_own"
on public.insurance_companies
for all
using (user_id = auth.uid())
with check (user_id = auth.uid());

drop policy if exists "policies_crud_own" on public.policies;
create policy "policies_crud_own"
on public.policies
for all
using (
    user_id = auth.uid()
    and exists (
        select 1 from public.clients
        where clients.id = policies.client_id
        and clients.user_id = auth.uid()
    )
    and exists (
        select 1 from public.insurance_companies
        where insurance_companies.id = policies.insurance_company_id
        and insurance_companies.user_id = auth.uid()
    )
)
with check (
    user_id = auth.uid()
    and exists (
        select 1 from public.clients
        where clients.id = policies.client_id
        and clients.user_id = auth.uid()
    )
    and exists (
        select 1 from public.insurance_companies
        where insurance_companies.id = policies.insurance_company_id
        and insurance_companies.user_id = auth.uid()
    )
);

drop policy if exists "quotes_crud_own" on public.quotes;
create policy "quotes_crud_own"
on public.quotes
for all
using (
    user_id = auth.uid()
    and exists (
        select 1 from public.clients
        where clients.id = quotes.client_id
        and clients.user_id = auth.uid()
    )
    and exists (
        select 1 from public.insurance_companies
        where insurance_companies.id = quotes.insurance_company_id
        and insurance_companies.user_id = auth.uid()
    )
)
with check (
    user_id = auth.uid()
    and exists (
        select 1 from public.clients
        where clients.id = quotes.client_id
        and clients.user_id = auth.uid()
    )
    and exists (
        select 1 from public.insurance_companies
        where insurance_companies.id = quotes.insurance_company_id
        and insurance_companies.user_id = auth.uid()
    )
);
