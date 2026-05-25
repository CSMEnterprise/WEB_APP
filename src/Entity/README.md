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

Le Entity sono introdotte gradualmente: i service possono ancora restituire array compatibili con le view esistenti, ma espongono anche metodi che restituiscono oggetti quando il controller o la logica applicativa sono pronti a usarli.

Primo passo gia applicato:

- `AnnuncioService` mantiene i metodi storici basati su array;
- `AnnuncioService` espone metodi equivalenti che restituiscono `EAnnuncio`;
- `AnnuncioController` usa `EAnnuncio` per leggere proprieta e stato dell'annuncio senza rompere le view.

Passi successivi applicati:

- `UserService` espone `EUtenteRegistrato` ed `EIndirizzo` e usa `EIndirizzo` in alcuni controlli sugli indirizzi;
- `CategoryService` espone `ECategoria`;
- `BusinessService` espone `EAccountBusiness`, `EIndirizzo` ed `EPagamento`;
- `CartService` espone `ECarrello`/`EElementoCarrello` e usa `EAnnuncio` quando controlla gli articoli;
- `WishlistService` espone `EPreferito` e usa `EAnnuncio` quando controlla i preferiti;
- `PaymentService` usa `EAnnuncio` ed `EPagamento` durante la conferma acquisto;
- `FeedbackService` usa `EFeedback` in creazione e lettura;
- `AdminService` espone `EAdmin`/`EModera` e usa `EAdmin` nei controlli di moderazione;
- `SegnalazioneService` usa `ESegnalazione` in creazione e lettura.

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
