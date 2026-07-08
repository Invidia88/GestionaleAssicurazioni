# Gestionale Assicurazioni

Web app Laravel per gestire clienti, compagnie assicurative, polizze e scadenze. Il progetto e pensato per una singola persona o una piccola agenzia e include interfaccia responsive per desktop e smartphone.

## Tecnologie usate

- Laravel 12 per backend, rotte, autenticazione, database e logica applicativa.
- Vue.js 3 per interfacce dinamiche.
- Inertia.js per collegare Laravel e Vue senza API separate.
- Tailwind CSS per UI responsive e manutenzione rapida.
- MySQL come database principale.
- Laravel Breeze per login, logout, registrazione e area protetta.
- Eloquent ORM per relazioni tra clienti, compagnie e polizze.
- Git per versionamento.

## Requisiti di sistema

- PHP 8.3 o superiore.
- Composer.
- Node.js e npm.
- MySQL 8 o compatibile.

## Installazione

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Configurazione `.env`

Creare un database MySQL chiamato `gestionale_assicurazioni`, poi configurare:

```env
APP_NAME="Gestionale Assicurazioni"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestionale_assicurazioni
DB_USERNAME=root
DB_PASSWORD=
```

## Migration e seed

```bash
php artisan migrate --seed
```

Il seed crea:

- 1 utente admin.
- 20 clienti.
- 5 compagnie assicurative.
- 40 polizze con stati attivi, in scadenza, scaduti e rinnovati.

Credenziali demo:

- Email: `admin@example.com`
- Password: `password`

## Avvio progetto

In due terminali:

```bash
php artisan serve
npm run dev
```

Aprire `http://127.0.0.1:8000`.

## Funzionalita disponibili

- Autenticazione con area protetta.
- Dashboard con riepilogo clienti, compagnie, polizze e scadenze.
- CRUD completo clienti.
- CRUD completo compagnie assicurative.
- CRUD completo polizze.
- Scadenziario con ricerca e filtri per periodo, compagnia, tipo e stato.
- Promemoria WhatsApp con messaggio precompilato.
- Layout responsive con sidebar desktop e menu mobile.
- Dati demo tramite seeder.
