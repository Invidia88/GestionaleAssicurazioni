# Changelog

## 2026-07-10

- Preparata la base di migrazione verso Supabase + Vercel.
- Aggiunte migration SQL Supabase per profili, clienti, compagnie, polizze e preventivi.
- Aggiunte policy Row Level Security per isolare i dati per utente autenticato.
- Aggiunto seed demo Supabase per ambiente staging.
- Aggiunta configurazione esempio `.env.supabase.example`.
- Aggiunta guida `DEPLOYMENT.md` per ambienti staging/production su Supabase e frontend su Vercel.

File principali modificati:

- `supabase/migrations/202607100001_init_schema.sql`
- `supabase/migrations/202607100002_rls_policies.sql`
- `supabase/seed.sql`
- `supabase/README.md`
- `.env.supabase.example`
- `DEPLOYMENT.md`
- `TECH_NOTES.md`
- `TODO.md`

## 2026-07-10

- Aggiunto flusso “Crea preventivo” per recupero clienti dopo la finestra di rinnovo di 15 giorni.
- Le polizze non rinnovate mostrano il bottone preventivo quando sono scadute da almeno 15 giorni.
- Il bottone apre una scelta compagnia e collega alla scheda della compagnia selezionata con pannello dedicato al preventivo.
- Il form polizza supporta la modalità preventivo con cliente, compagnia, tipo, date e note precompilate dalla polizza scaduta.

File principali modificati:

- `app/Models/Policy.php`
- `app/Http/Controllers/PolicyController.php`
- `app/Http/Controllers/InsuranceCompanyController.php`
- `resources/js/Components/CreateQuoteButton.vue`
- `resources/js/Pages/Policies/Index.vue`
- `resources/js/Pages/Policies/Show.vue`
- `resources/js/Pages/Policies/Form.vue`
- `resources/js/Pages/Expirations/Index.vue`
- `resources/js/Pages/Companies/Show.vue`

## 2026-07-08

- Rifatta in modo piu completo la UI/UX del gestionale con impostazione CRM/SaaS professionale.
- Separato il layout in componenti riutilizzabili: `Sidebar`, `Topbar`, `AppLayout`, `StatCard`, `EmptyState`, `DataTableWrapper`, `FormCard`, `WhatsAppButton`.
- Aggiornati bottoni coerenti per azioni primarie blu, secondarie grigie, pericolo rosse e WhatsApp verde.
- Migliorati dashboard, scadenziario, tabelle elenco, card mobile, form e pagine dettaglio.
- Reso lo scadenziario piu centrale con riepiloghi, filtri organizzati, evidenziazione scadute/in scadenza e azioni rapide.
- Migliorata accessibilita base con focus visibile, dimensioni touch-friendly e contrasti piu leggibili.

File principali modificati:

- `resources/js/Components/Sidebar.vue`
- `resources/js/Components/Topbar.vue`
- `resources/js/Components/StatCard.vue`
- `resources/js/Components/EmptyState.vue`
- `resources/js/Components/DataTableWrapper.vue`
- `resources/js/Components/FormCard.vue`
- `resources/js/Components/WhatsAppButton.vue`
- `resources/js/Layouts/AppLayout.vue`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/Pages/Expirations/Index.vue`
- `resources/js/Pages/Clients/*`
- `resources/js/Pages/Companies/*`
- `resources/js/Pages/Policies/*`
- `resources/css/app.css`

## 2026-07-08

- Modernizzata la grafica generale con sidebar scura, topbar più pulita, card con ombre leggere e form/tabelle più rifiniti.
- Aggiornata la schermata di login con branding dedicato e testi in italiano.
- Rifinita la dashboard con metriche più leggibili, badge e link rapidi.

File principali modificati:

- `resources/css/app.css`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `resources/js/Layouts/GuestLayout.vue`
- `resources/js/Pages/Auth/Login.vue`
- `resources/js/Pages/Dashboard.vue`
- `resources/js/Components/PrimaryButton.vue`
- `resources/js/Components/TextInput.vue`
- `resources/js/Components/StatusBadge.vue`

## 2026-07-08

- Creato progetto Laravel 12 con Breeze, Vue 3, Inertia e Tailwind.
- Configurata autenticazione con pagine protette.
- Aggiunte migration per `clients`, `insurance_companies` e `policies`.
- Implementati model Eloquent con relazioni cliente-polizze e compagnia-polizze.
- Implementati controller, request di validazione, factory e seeder.
- Implementati CRUD clienti, compagnie e polizze.
- Implementata dashboard con conteggi e liste operative.
- Implementato scadenziario con ricerca e filtri.
- Implementato link WhatsApp con messaggio precompilato.
- Aggiornata UI responsive con sidebar desktop, menu mobile, card, tabelle e badge stato.
- Aggiornata documentazione.

File principali modificati:

- `routes/web.php`
- `app/Models/Client.php`
- `app/Models/InsuranceCompany.php`
- `app/Models/Policy.php`
- `app/Http/Controllers/*Controller.php`
- `app/Http/Requests/*Request.php`
- `database/migrations/*`
- `database/factories/*`
- `database/seeders/DatabaseSeeder.php`
- `resources/js/Layouts/AuthenticatedLayout.vue`
- `resources/js/Pages/**`
- `resources/js/Components/StatusBadge.vue`
- `resources/js/Components/Pagination.vue`
- `.env.example`
- `README.md`
- `TECH_NOTES.md`
- `TODO.md`
