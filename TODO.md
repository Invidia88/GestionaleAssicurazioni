# TODO

## Completato

- Setup Laravel 12, Vue 3, Inertia, Tailwind e Breeze.
- Autenticazione e area protetta.
- Database per clienti, compagnie e polizze.
- CRUD clienti.
- CRUD compagnie assicurative.
- CRUD polizze.
- Dashboard riepilogativa.
- Scadenziario con filtri.
- Pulsante promemoria WhatsApp.
- UI responsive desktop/mobile.
- UI/UX CRM moderna con componenti riutilizzabili.
- Tabelle desktop e card mobile per gli elenchi principali.
- Scadenziario evidenziato con riepiloghi, filtri e azioni rapide.
- Flusso “Crea preventivo” per recupero cliente dopo 15 giorni dalla scadenza.
- Preparazione migrazione Supabase con schema SQL, RLS, seed demo e guida deploy Vercel.
- Conversione iniziale frontend su `staging` a Vue SPA con Supabase Auth, Vue Router e output Vercel `dist`.
- Seeder demo con credenziali `admin@example.com` / `password`.
- Documentazione iniziale.

## Ancora da fare

- Creare i progetti Supabase `gestionale-assicurazioni-staging` e `gestionale-assicurazioni-production`.
- Eseguire migration e RLS Supabase nei due ambienti.
- Testare in staging tutte le query Supabase con dati reali e RLS attivo.
- Rifinire validazioni e messaggi errore della SPA.
- Aggiungere modifica completa dei preventivi, non solo creazione/eliminazione.
- Configurare Vercel con variabili ambiente separate per staging e production.
- Definire policy di backup database prima di usare dati reali in produzione.
- Valutare disabilitazione registrazione pubblica se l'agenzia vuole creare utenti solo manualmente.
- Verificare con utenti reali se i filtri dello scadenziario coprono tutti i casi operativi.
- Collegare la tabella Supabase `quotes` al flusso preventivi quando il frontend sara convertito a SPA.

## Idee future

- Ruoli e permessi.
- Esportazione dati.
- Allegati documentali.
- Notifiche automatiche.
- Storico rinnovi.
- Pipeline recupero clienti con stati “da contattare”, “preventivo inviato”, “perso”, “recuperato”.
- Tema chiaro/scuro opzionale.
- Test browser automatici sulle principali risoluzioni mobile.

## Bug noti

- Nessun bug noto al momento.
