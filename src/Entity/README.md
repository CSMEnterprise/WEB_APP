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

Le Entity sono usate dai controller e dal package Foundation, mentre le view PHP esistenti ricevono ancora array per non obbligare a riscrivere tutta l'interfaccia in una sola volta.

La persistenza viene spostata nel package `App\Foundation`, seguendo la logica del progetto FillSpace:

- `FDataBase` mantiene l'accesso al database;
- `FPersistentManager` e' la facciata unica usata dal livello applicativo;
- le classi tabella `F...` fanno da mapper tra PDO e Entity.

Queste classi leggono righe dal database, restituiscono oggetti `E...` e salvano gli oggetti filtrando solo le colonne della tabella.
Le validazioni di input restano nei controller; le transazioni e la persistenza stanno nel package `App\Foundation`.

`App\View\ViewDataNormalizer` contiene la conversione da Entity ad array. In questo modo i controller possono passare oggetti al layer View, e la compatibilita con i template Smarty resta confinata al confine di rendering.

`EBaseEntity` conserva anche i campi extra letti dalle query con join, per esempio `categoria_nome`, `immagine_principale`, `venditore_username`, `annuncio_titolo`. Questo evita di perdere dati utili alle pagine quando una riga del database viene trasformata in Entity.

Stato attuale:

- i controller principali chiamano `FPersistentManager` e lavorano sulle Entity;
- le view restano compatibili perche `SmartyView` normalizza Entity e array prima di assegnarli ai template;
- `FDataBase` e `FPersistentManager` riprendono la struttura Foundation del progetto di riferimento;
- `FBaseTable` contiene le operazioni comuni di mapping tabella/Entity;
- `FAnnuncio`, `FImmagine`, `FCategoria`, `FUtenteRegistrato`, `FIndirizzo`, `FAccountBusiness`, `FCarrello`, `FElementoCarrello`, `FPreferito`, `FFeedback`, `FSegnalazione`, `FAdmin`, `FModera`, `FPagamento` e `FPasswordReset` gestiscono le tabelle migrate nel package Foundation;
- le Entity sono il punto di passaggio tra dati del database e logica applicativa.

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
