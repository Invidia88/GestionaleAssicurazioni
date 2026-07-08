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

## Miglioramenti futuri

- Ruoli distinti admin/operatore con permessi separati.
- Import/export Excel.
- Invio promemoria automatici programmati.
- Allegati PDF per documenti polizza.
- Storico rinnovi collegato alla polizza precedente.
- Audit log delle modifiche.
