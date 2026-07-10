# Deployment Supabase + Vercel

Questa guida prepara il passaggio da Laravel + Inertia a Vue SPA + Supabase + Vercel.

## Decisione ambienti

Supabase Free consente 2 progetti attivi e 500 MB database per progetto. I progetti free vengono messi in pausa dopo 1 settimana di inattivita. Per questo conviene usare esattamente due progetti:

- `gestionale-assicurazioni-staging`
- `gestionale-assicurazioni-production`

Puoi crearli in due organizzazioni separate, come richiesto:

- organizzazione `Gestionale Assicurazioni Staging`
- organizzazione `Gestionale Assicurazioni Production`

Scelta piu semplice da gestire: una sola organizzazione con due progetti. La separazione vera tra ambienti e comunque garantita dai due progetti Supabase distinti.

Riferimenti:

- Supabase pricing: https://supabase.com/pricing
- Supabase RLS: https://supabase.com/docs/guides/database/postgres/row-level-security
- Vercel pricing: https://vercel.com/pricing

## Setup Supabase staging

1. Apri Supabase e crea progetto `gestionale-assicurazioni-staging`.
2. Vai in `SQL Editor`.
3. Esegui `supabase/migrations/202607100001_init_schema.sql`.
4. Esegui `supabase/migrations/202607100002_rls_policies.sql`.
5. Vai in `Authentication > Users` e crea l'utente admin.
6. Copia lo UUID dell'utente.
7. Apri `supabase/seed.sql`, sostituisci il placeholder UUID e lancia il seed.
8. Salva `Project URL` e `anon public key`.

## Setup Supabase production

Ripeti gli stessi passaggi sul progetto `gestionale-assicurazioni-production`.

In produzione evita il seed demo, salvo se serve una demo cliente pulita.

## Setup Vercel

1. Crea un progetto Vercel collegato al repository Git.
2. Framework preset: `Vite`.
3. Build command: `npm run build`.
4. Per l'app Laravel attuale `public/build` contiene solo gli asset compilati, non un frontend standalone completo.

Quando il frontend sara convertito in Vue SPA pura, Vercel diventera il deploy frontend reale e l'output directory sara `dist`.

## Variabili ambiente Vercel

Staging:

```env
VITE_SUPABASE_URL=https://staging-project-ref.supabase.co
VITE_SUPABASE_ANON_KEY=staging-anon-key
VITE_APP_ENV=staging
VITE_APP_NAME="Gestionale Assicurazioni"
```

Production:

```env
VITE_SUPABASE_URL=https://production-project-ref.supabase.co
VITE_SUPABASE_ANON_KEY=production-anon-key
VITE_APP_ENV=production
VITE_APP_NAME="Gestionale Assicurazioni"
```

Non mettere mai service role key in Vercel frontend.

## Step successivo: conversione frontend

L'app attuale usa Laravel + Inertia. Per usare Vercel come frontend reale bisogna fare uno step successivo:

1. Installare `@supabase/supabase-js`.
2. Creare `resources/js/lib/supabase.js`.
3. Introdurre Vue Router.
4. Convertire login/logout su Supabase Auth.
5. Convertire dashboard, clienti, compagnie, polizze, scadenziario e preventivi da props Inertia a query Supabase.
6. Cambiare build Vercel verso una SPA Vite con output `dist`.

## Nota piano free

Il piano free va bene per sviluppo, staging e demo leggere. Per un cliente reale conviene pianificare Supabase Pro prima di usare dati di produzione importanti, soprattutto per backup automatici, log retention e progetto non pausato.
