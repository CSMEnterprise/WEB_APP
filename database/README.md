# Database NerdVault

Per creare il database da zero importa prima `nerdvault.sql`.

Per ottenere anche un ambiente pronto per la demo d'esame, importa subito dopo:

```text
dati_demo.sql
```

Lo script demo usa gli ID compresi tra 9000 e 9999, quindi puo essere
reimportato senza cancellare dati applicativi con ID differenti.

Se esiste gia un database `nerdvault`, fai prima un backup e rimuovi il vecchio database prima di importare lo schema.

Il file contiene:

- creazione del database `nerdvault`;
- creazione ordinata di tutte le tabelle;
- chiavi primarie, indici, vincoli univoci e foreign key gia dentro i `CREATE TABLE`;
- dati base della tabella `categoria`.

Non ci sono file di migrazione da importare dopo lo schema principale.

## Contenuto del dataset demo

Il dataset contiene:

- due amministratori, di livello 1 e 2;
- utenti verificati, un utente bannato e uno non ancora verificato;
- un account business verificato;
- dodici annunci attivi e venduti con immagini versionate;
- indirizzi, carrelli e wishlist gia popolati;
- pagamenti completati e relativi feedback;
- segnalazioni aperte, in revisione e risolte;
- storico delle azioni di moderazione.

## Credenziali demo

Tutti gli account usano la password `Demo2026!`.

| Ruolo | Email |
| --- | --- |
| Utente standard | `alice.demo@nerdvault.test` |
| Utente standard | `giulia.demo@nerdvault.test` |
| Venditore privato | `marco.demo@nerdvault.test` |
| Account business | `business.demo@nerdvault.test` |
| Admin livello 1 | `admin.demo@nerdvault.test` |
| Admin livello 2 | `superadmin.demo@nerdvault.test` |
| Utente bannato | `banned.demo@nerdvault.test` |
| Email non verificata | `nonverificato.demo@nerdvault.test` |

Le immagini usate dal dataset sono in `public/assets/demo/` e sono incluse nel repository.
