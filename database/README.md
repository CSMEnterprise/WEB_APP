# Database NerdVault

Per creare il database da zero importa solo `nerdvault.sql`.

Se esiste gia un database `nerdvault`, fai prima un backup e rimuovi il vecchio database prima di importare lo schema.

Il file contiene:

- creazione del database `nerdvault`;
- creazione ordinata di tutte le tabelle;
- chiavi primarie, indici, vincoli univoci e foreign key gia dentro i `CREATE TABLE`;
- dati base della tabella `categoria`.

Non ci sono file di migrazione da importare dopo lo schema principale.

Se in futuro verranno aggiunti dati dimostrativi, vanno messi in un file separato, per esempio `dati_demo.sql`, lasciando `nerdvault.sql` dedicato allo schema e alle categorie di base.
