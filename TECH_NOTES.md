# Note tecniche

## Architettura

Il progetto usa Laravel come applicazione principale. Le rotte web ricevono le richieste, i controller leggono o modificano i dati tramite Eloquent e restituiscono pagine Inertia. Le pagine sono componenti Vue in `resources/js/Pages`.

## Laravel, Vue e Inertia

Inertia permette ai controller Laravel di renderizzare componenti Vue passando props. Non serve creare un'API REST separata per questa versione del gestionale. Laravel gestisce sessione, autenticazione, validazione e redirect; Vue gestisce form, tabelle, filtri e navigazione client-side.

## Relazioni dati

- `Client` ha molte `Policy`.
- `InsuranceCompany` ha molte `Policy`.
- `Policy` appartiene a un `Client`.
- `Policy` appartiene a una `InsuranceCompany`.

Le foreign key sono definite nella migration `policies` e usano `cascadeOnDelete`, quindi eliminare un cliente o una compagnia elimina anche le polizze collegate.

## Scadenziario

Lo scadenziario usa `ExpirationController`. La query carica cliente e compagnia, ordina per `end_date` e applica filtri:

- ricerca su numero polizza, nome, cognome, telefono e codice fiscale cliente;
- oggi;
- entro 7 giorni;
- entro 30 giorni;
- scadute;
- compagnia;
- tipo polizza;
- stato.

I conteggi dashboard usano gli scope `expiringWithin()` e `expired()` definiti nel model `Policy`.

## Link WhatsApp

Il model `Policy` espone l'attributo `whatsapp_url`. Il telefono del cliente viene normalizzato togliendo spazi e simboli non necessari. Il link generato usa `https://wa.me/` con testo precompilato:

`Ciao [nome_cliente], ti ricordiamo che la tua polizza [tipo_polizza] con compagnia [nome_compagnia] scadrà il [data_scadenza]. Contattaci per il rinnovo.`

Il link e disponibile solo quando la polizza viene caricata con cliente e compagnia e il cliente ha un telefono.

## Flusso preventivo recupero cliente

Una polizza espone gli attributi `can_create_quote` e `quote_available_from`. Il preventivo diventa disponibile quando la polizza e scaduta da almeno 15 giorni e non e marcata come `rinnovata`.

Il componente `CreateQuoteButton`:

- compare sulle polizze idonee;
- apre una modale per scegliere la compagnia assicurativa;
- porta alla scheda della compagnia scelta con `quote_policy_id`;
- dalla scheda compagnia mostra un pannello “Preventivo recupero cliente”;
- apre il form polizza in modalita preventivo con `source_policy_id` e `insurance_company_id`.

Il form polizza usa la prop `mode = quote` per cambiare titolo, descrizione e valori iniziali. Il modulo resta basato sulle polizze esistenti per non introdurre un nuovo dominio dati prima che sia necessario.

## Struttura UI

La UI Vue e organizzata con componenti riutilizzabili:

- `AuthenticatedLayout` gestisce lo scheletro delle pagine protette.
- `AppLayout` e un alias semantico del layout admin.
- `Sidebar` contiene navigazione desktop, logo e informazioni utente.
- `Topbar` contiene titolo pagina, menu mobile, flash message e menu utente.
- `StatCard` standardizza le card statistiche della dashboard e dello scadenziario.
- `StatusBadge` centralizza i colori degli stati polizza.
- `DataTableWrapper` applica contenitore, bordo, ombra e scroll orizzontale alle tabelle desktop.
- `EmptyState` mostra stati vuoti coerenti e piu chiari.
- `FormCard` incornicia i form con titolo, descrizione e padding uniforme.
- `WhatsAppButton` centralizza il bottone verde per i promemoria.
- `CreateQuoteButton` centralizza la scelta compagnia per i preventivi di recupero.

Le pagine elenco usano tabelle su desktop e card dedicate su mobile, cosi il contenuto resta leggibile senza uscire dallo schermo. I form usano griglie responsive: due colonne su desktop e colonna singola su smartphone.

## Miglioramenti futuri

- Ruoli distinti admin/operatore con permessi separati.
- Import/export Excel.
- Invio promemoria automatici programmati.
- Allegati PDF per documenti polizza.
- Storico rinnovi collegato alla polizza precedente.
- Audit log delle modifiche.
- Aggiungere test visuali end-to-end per desktop e mobile.
