# System Sequence Diagram (SSD) - NerdVault

Questi diagrammi descrivono, per ciascun caso d'uso del documento
`NerdVault_Use_Cases_Documentation_Coerente.docx`, l'interazione tra l'attore
(`User`) e il sistema visto come scatola nera (`System`), secondo la convenzione
del corso:

- ogni **operazione di sistema** corrisponde a una **richiesta HTTP** del client
  (annotata tra `< >`, es. `<POST /annuncio/store>`);
- le **frecce piene** sono le operazioni invocate dall'attore (con i parametri);
- le **frecce tratteggiate** sono i valori/risposte restituiti dal sistema;
- le operazioni che **modificano lo stato** usano `POST` e includono il
  `csrfToken` tra i parametri (protezione CSRF, coerente con il progetto).

## Elenco

| File | Caso d'uso |
|------|------------|
| `UC1_RicercaAnnunci.puml`            | UC1 - Cercare e visualizzare annunci |
| `UC2_RegistrazioneUtente.puml`       | UC2 - Registrarsi come utente standard |
| `UC3_LoginLogout.puml`               | UC3 - Effettuare login e logout |
| `UC4_RecuperoPassword.puml`          | UC4 - Recuperare o reimpostare password |
| `UC5_ProfiloIndirizzi.puml`          | UC5 - Gestire profilo e indirizzi |
| `UC6_CreaAnnuncio.puml`              | UC6 - Creare un annuncio |
| `UC7_ModificaEliminaAnnuncio.puml`   | UC7 - Modificare o eliminare un annuncio |
| `UC8_Wishlist.puml`                  | UC8 - Gestire wishlist |
| `UC9_Carrello.puml`                  | UC9 - Gestire carrello |
| `UC10_CheckoutPagamento.puml`        | UC10 - Effettuare checkout e pagamento simulato |
| `UC11_Feedback.puml`                 | UC11 - Lasciare feedback su un acquisto |
| `UC12_Segnalazione.puml`             | UC12 - Aprire una segnalazione |
| `UC13_AccountBusiness.puml`          | UC13 - Gestire account business |
| `UC14_AdminUtentiSegnalazioni.puml`  | UC14 - Amministrare utenti e segnalazioni |
| `UC15_ModerazioneAvanzata.puml`      | UC15 - Gestire moderazione avanzata |

## Come generare le immagini

I file sono in formato [PlantUML](https://plantuml.com).

- **VS Code**: estensione "PlantUML" -> apri il `.puml` -> `Alt+D` per l'anteprima.
- **Online**: incolla il contenuto su <https://www.plantuml.com/plantuml>.
- **CLI** (richiede Java + plantuml.jar):

  ```bash
  java -jar plantuml.jar INFO/SSD/*.puml          # genera PNG
  java -jar plantuml.jar -tsvg INFO/SSD/*.puml    # genera SVG
  ```
