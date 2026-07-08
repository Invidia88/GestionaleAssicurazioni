# Changelog

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
