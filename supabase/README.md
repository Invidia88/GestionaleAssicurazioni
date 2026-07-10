# Supabase

Questa cartella contiene gli artefatti per spostare database e backend dati su Supabase.

## File

- `migrations/202607100001_init_schema.sql`: schema Postgres, tipi enum, indici, trigger e helper.
- `migrations/202607100002_rls_policies.sql`: policy Row Level Security.
- `seed.sql`: dati demo minimi per staging.

## Ordine di esecuzione

Eseguire in ogni progetto Supabase:

1. `202607100001_init_schema.sql`
2. `202607100002_rls_policies.sql`
3. `seed.sql` solo se servono dati demo

Prima di lanciare `seed.sql`, creare un utente da Supabase Auth e sostituire il placeholder `00000000-0000-0000-0000-000000000000` con l'id reale dell'utente.

## Nota architetturale

Il progetto Laravel attuale resta funzionante. Questi file preparano la migrazione verso:

- Supabase Auth
- Supabase Postgres
- Vue SPA su Vercel

La conversione completa del frontend richiede uno step successivo: rimuovere Inertia e consumare Supabase tramite `@supabase/supabase-js`.
