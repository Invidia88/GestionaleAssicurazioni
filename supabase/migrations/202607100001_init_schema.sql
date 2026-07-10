-- Supabase/Postgres schema for Gestionale Assicurazioni.
-- Run this in both staging and production projects.

create extension if not exists pgcrypto;

do $$
begin
    if not exists (select 1 from pg_type where typname = 'app_role') then
        create type app_role as enum ('admin', 'operator');
    end if;

    if not exists (select 1 from pg_type where typname = 'policy_status') then
        create type policy_status as enum ('attiva', 'in_scadenza', 'scaduta', 'rinnovata');
    end if;

    if not exists (select 1 from pg_type where typname = 'quote_status') then
        create type quote_status as enum ('bozza', 'inviato', 'accettato', 'rifiutato', 'scaduto');
    end if;
end $$;

create table if not exists public.profiles (
    id uuid primary key references auth.users(id) on delete cascade,
    name text not null,
    email text not null,
    role app_role not null default 'operator',
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table if not exists public.clients (
    id uuid primary key default gen_random_uuid(),
    user_id uuid not null references auth.users(id) on delete cascade,
    first_name text not null,
    last_name text not null,
    phone text,
    email text,
    tax_code text,
    address text,
    city text,
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint clients_tax_code_user_unique unique (user_id, tax_code)
);

create table if not exists public.insurance_companies (
    id uuid primary key default gen_random_uuid(),
    user_id uuid not null references auth.users(id) on delete cascade,
    name text not null,
    contact_name text,
    contact_phone text,
    contact_email text,
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint insurance_companies_name_user_unique unique (user_id, name)
);

create table if not exists public.policies (
    id uuid primary key default gen_random_uuid(),
    user_id uuid not null references auth.users(id) on delete cascade,
    client_id uuid not null references public.clients(id) on delete cascade,
    insurance_company_id uuid not null references public.insurance_companies(id) on delete cascade,
    type text not null check (type in ('Auto', 'Moto', 'Casa', 'Vita', 'Infortuni', 'Aziendale', 'Altro')),
    number text not null,
    start_date date not null,
    end_date date not null,
    annual_premium numeric(10, 2) not null default 0,
    status policy_status not null default 'attiva',
    notes text,
    source_policy_id uuid references public.policies(id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint policies_number_user_unique unique (user_id, number),
    constraint policies_date_check check (end_date >= start_date)
);

create table if not exists public.quotes (
    id uuid primary key default gen_random_uuid(),
    user_id uuid not null references auth.users(id) on delete cascade,
    source_policy_id uuid references public.policies(id) on delete set null,
    client_id uuid not null references public.clients(id) on delete cascade,
    insurance_company_id uuid not null references public.insurance_companies(id) on delete cascade,
    type text not null check (type in ('Auto', 'Moto', 'Casa', 'Vita', 'Infortuni', 'Aziendale', 'Altro')),
    number text not null,
    start_date date,
    end_date date,
    annual_premium numeric(10, 2),
    status quote_status not null default 'bozza',
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint quotes_number_user_unique unique (user_id, number),
    constraint quotes_date_check check (end_date is null or start_date is null or end_date >= start_date)
);

create index if not exists clients_user_last_name_idx on public.clients(user_id, last_name, first_name);
create index if not exists companies_user_name_idx on public.insurance_companies(user_id, name);
create index if not exists policies_user_end_date_idx on public.policies(user_id, end_date);
create index if not exists policies_user_status_idx on public.policies(user_id, status);
create index if not exists policies_client_idx on public.policies(client_id);
create index if not exists policies_company_idx on public.policies(insurance_company_id);
create index if not exists quotes_user_status_idx on public.quotes(user_id, status);
create index if not exists quotes_source_policy_idx on public.quotes(source_policy_id);

create or replace function public.set_updated_at()
returns trigger
language plpgsql
as $$
begin
    new.updated_at = now();
    return new;
end;
$$;

drop trigger if exists profiles_set_updated_at on public.profiles;
create trigger profiles_set_updated_at
before update on public.profiles
for each row execute function public.set_updated_at();

drop trigger if exists clients_set_updated_at on public.clients;
create trigger clients_set_updated_at
before update on public.clients
for each row execute function public.set_updated_at();

drop trigger if exists companies_set_updated_at on public.insurance_companies;
create trigger companies_set_updated_at
before update on public.insurance_companies
for each row execute function public.set_updated_at();

drop trigger if exists policies_set_updated_at on public.policies;
create trigger policies_set_updated_at
before update on public.policies
for each row execute function public.set_updated_at();

drop trigger if exists quotes_set_updated_at on public.quotes;
create trigger quotes_set_updated_at
before update on public.quotes
for each row execute function public.set_updated_at();

create or replace function public.handle_new_user()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
    insert into public.profiles (id, name, email, role)
    values (
        new.id,
        coalesce(new.raw_user_meta_data->>'name', split_part(new.email, '@', 1)),
        new.email,
        'admin'
    )
    on conflict (id) do nothing;

    return new;
end;
$$;

drop trigger if exists on_auth_user_created on auth.users;
create trigger on_auth_user_created
after insert on auth.users
for each row execute function public.handle_new_user();

create or replace function public.can_create_recovery_quote(policy_end_date date, policy_status policy_status)
returns boolean
language sql
stable
as $$
    select policy_status <> 'rinnovata'
        and policy_end_date + interval '15 days' <= current_date;
$$;
