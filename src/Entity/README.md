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
Le validazioni, le transazioni e le regole applicative stanno nei controller; la persistenza resta nel package `App\Foundation`.

`App\Controllers\BaseController` contiene i metodi di conversione da Entity ad array. In questo modo i controller possono ragionare sugli oggetti, e la conversione resta confinata al confine con le view.

`EBaseEntity` conserva anche i campi extra letti dalle query con join, per esempio `categoria_nome`, `immagine_principale`, `venditore_username`, `annuncio_titolo`. Questo evita di perdere dati utili alle pagine quando una riga del database viene trasformata in Entity.

Stato attuale:

- i controller principali chiamano `FPersistentManager` e lavorano sulle Entity;
- le view restano compatibili perche ricevono array generati dalle Entity;
- `FDataBase` e `FPersistentManager` riprendono la struttura Foundation del progetto di riferimento;
- `FBaseTable` contiene le operazioni comuni di mapping tabella/Entity;
- `FAnnuncio`, `FImmagine`, `FCategoria`, `FUtenteRegistrato`, `FIndirizzo`, `FAccountBusiness`, `FCarrello`, `FElementoCarrello`, `FPreferito`, `FFeedback`, `FSegnalazione`, `FAdmin`, `FModera` e `FPagamento` gestiscono le prime tabelle migrate nel package Foundation;
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
