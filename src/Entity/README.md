# Entity

Questa cartella contiene le classi Entity del progetto NerdVault.

Le Entity rappresentano gli oggetti principali del dominio applicativo e sono basate sulle tabelle definite in `database/nerdvault.sql`.

Ogni Entity segue lo stile del modello usato a lezione:

- proprieta private;
- costruttore;
- metodi getter;
- metodi setter;
- metodi di utilita legati allo stato dell'oggetto;
- `toArray()`;
- `jsonSerialize()`;
- `__toString()`.

Le Entity sono state introdotte senza rimuovere i vecchi `src/models`, cosi l'app continua a funzionare durante la migrazione progressiva da array PDO a oggetti.

Mappatura principale:

- `EAdmin` -> tabella `admin`
- `EUtenteRegistrato` -> tabella `utente_registrato`
- `ECategoria` -> tabella `categoria`
- `EAccountBusiness` -> tabella `account_business`
- `EIndirizzo` -> tabella `indirizzi`
- `EAnnuncio` -> tabella `annuncio`
- `EImmagine` -> tabella `immagine`
- `ECarrello` -> tabella `carrello`
- `EElementoCarrello` -> tabella `elemento_carrello`
- `EPagamento` -> tabella `pagamento`
- `EFeedback` -> tabella `feedback`
- `EPasswordReset` -> tabella `password_reset`
- `EPreferito` -> tabella `preferito`
- `ESegnalazione` -> tabella `segnalazione`
- `EModera` -> tabella `modera`
