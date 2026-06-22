# NerdVault - WEB_APP

NerdVault e' una web app marketplace sviluppata in PHP per la gestione di annunci, utenti, wishlist, carrello, pagamenti simulati, account business, feedback, segnalazioni e area amministratore.

Il progetto usa una struttura MVC leggera senza framework esterni: `public/index.php` fa solo da bootstrap, mentre il routing principale e' gestito dalla classe `App\Core\FrontController`. Controller, servizi, entity, viste, middleware e configurazione sono separati nella cartella `src`.

## Tecnologie

- PHP 8.x
- Apache
- MySQL / MariaDB
- PDO
- Composer
- Smarty
- HTML, CSS e JavaScript
- XAMPP per l'ambiente locale

## Struttura del progetto

```text
WEB_APP/
|-- public/
|   |-- css/
|   |-- index.php
|   `-- uploads/
|       `-- annunci/
|-- src/
|   |-- config/
|   |   |-- db.example.php      template versionato
|   |   `-- mail.example.php    template versionato
|   |-- controllers/
|   |-- Entity/
|   |-- Foundation/
|   |-- helpers/
|   |-- middleware/
|   `-- services/
|-- templates/
|   |-- admin/
|   |-- annunci/
|   |-- auth/
|   |-- business/
|   |-- carrello/
|   |-- errors/
|   |-- feedback/
|   |-- layouts/
|   |-- pagamenti/
|   |-- segnalazioni/
|   |-- utenti/
|   `-- wishlist/
|-- database/
|   |-- nerdvault.sql
|   `-- README.md
|-- INFO/
|   |-- Bozze UI/
|   |-- ER/
|   |-- SSD/
|   |-- UML/
|   `-- documenti PDF di progetto
|-- assets/
|-- .github/
`-- README.md
```

## Cartelle principali

- `public/`: contiene il punto di ingresso dell'applicazione. Apache deve servire questa cartella, non la root del progetto.
- `public/css/`: contiene gli stylesheet dell'interfaccia, separati dai template Smarty.
- `public/uploads/`: contiene i file caricati dagli utenti, per esempio le immagini degli annunci.
- `src/config/`: contiene i template di configurazione versionati. I file locali reali `db.php` e `mail.php` sono ignorati da Git.
- `src/controllers/`: riceve le richieste dal router e coordina servizi e viste.
- `src/Entity/`: contiene le classi Entity con proprieta private, getter, setter e metodi di utilita.
- `src/Foundation/`: contiene classi infrastrutturali e mapper tabella/Entity. La persistenza segue la logica `FDataBase` + `FPersistentManager` + classi `F...`.
- `src/View/`: contiene il wrapper della view layer, incluso `SmartyView` per il rendering Smarty.
- `src/services/`: contiene servizi applicativi di supporto, come email ed eccezioni applicative; le query al database stanno in `src/Foundation/`.
- `templates/`: contiene i template Smarty usati per renderizzare le pagine.
- `src/middleware/`: contiene i controlli di accesso per utenti autenticati, admin, business e guest.
- `src/helpers/`: contiene funzioni comuni, come l'escape HTML.
- `database/`: contiene lo schema SQL completo per creare il database.
- `INFO/`: contiene la documentazione di progetto consegnabile o consultabile durante la presentazione.

## Documentazione in INFO

La cartella `INFO/` raccoglie i materiali di analisi, progettazione e presentazione del progetto:

- `INFO/Bozze UI/`: contiene le bozze PDF delle principali schermate dell'applicazione, come home, login, registrazione, profilo, carrello, checkout, wishlist, feedback e dashboard amministratore.
- `INFO/ER/`: contiene lo schema Entity-Relationship del database, utile per spiegare modello concettuale, tabelle principali, relazioni e vincoli.
- `INFO/SSD/`: contiene i System Sequence Diagram dei principali casi d'uso. I file `.puml` descrivono i singoli flussi, mentre `SSD_NerdVault.pdf` raccoglie la versione esportata.
- `INFO/UML/`: contiene i diagrammi PlantUML dell'architettura generale, del layer Foundation/persistenza e del routing/controller.
- `INFO/NerdVault_Architettura.pdf`: documento di supporto sull'architettura dell'applicazione.
- `INFO/NerdVault_Use_Cases_Documentation_Coerente.pdf`: documento dei casi d'uso.
- `INFO/presentazione_progetto_Minati_Calabrese_Scipioni(V2).pdf`: documento di presentazione del progetto.
- `INFO/LICENSE`: licenza del materiale di progetto.

Questi file non sono necessari per l'esecuzione dell'applicazione, ma servono per analizzare e presentare il lavoro svolto: casi d'uso, bozze interfaccia, schema ER, diagrammi UML, SSD e documentazione finale.

## Namespace e autoload

Il progetto usa Composer anche per caricare le classi interne.

Namespace principali:

- `App\Core\` -> `src/Core/`
- `App\Controllers\` -> `src/controllers/`
- `App\Services\` -> `src/services/`
- `App\Entity\` -> `src/Entity/`
- `App\Foundation\` -> `src/Foundation/`
- `App\View\` -> `src/View/`

Il bootstrap `public/index.php` carica `vendor/autoload.php`; da li in poi controller, service, entity e foundation vengono caricati automaticamente. Le funzioni helper e middleware vengono caricate tramite la sezione `autoload.files` di `composer.json`.

Quando si aggiunge o rinomina una classe, eseguire:

```bash
composer dump-autoload
```

## Funzionalita' principali

- Registrazione utente normale
- Registrazione account business
- Login e logout
- Profilo utente
- Creazione, elenco, dettaglio ed eliminazione annunci
- Upload immagini per gli annunci
- Ricerca annunci
- Wishlist
- Carrello
- Checkout e pagamento simulato
- Feedback
- Segnalazioni
- Dashboard amministratore
- Gestione utenti da admin
- Gestione segnalazioni da admin

## Configurazione locale con XAMPP

### 1. Clonare il progetto

Clonare la repository dentro la cartella `htdocs` di XAMPP:

```bash
cd C:\xampp\htdocs
git clone https://github.com/CSMEnterprise/WEB_APP.git
```

Entrare nella cartella del progetto:

```bash
cd WEB_APP
```

Installare le dipendenze Composer:

```bash
composer install
```

### 2. Usare il branch di sviluppo

Il branch di lavoro del progetto e' `develop`:

```bash
git checkout develop
git pull origin develop
```

### 3. Importare il database

1. Avviare MySQL/MariaDB da XAMPP.
2. Aprire phpMyAdmin.
3. Importare il file:

```text
database/nerdvault.sql
```

Il file crea automaticamente il database `nerdvault`, tutte le tabelle, le chiavi, i vincoli e le categorie di base.

Per installare anche i dati dimostrativi usati durante la presentazione,
importare subito dopo:

```text
database/dati_demo.sql
```

Il dataset aggiunge account per tutti i ruoli, annunci attivi e venduti,
carrelli, wishlist, pagamenti, feedback e segnalazioni. Le credenziali sono
elencate in `database/README.md`.

Se esiste gia un vecchio database `nerdvault` e vuoi ricrearlo da zero, esporta prima un backup e poi elimina il vecchio database da phpMyAdmin prima di importare questo file.

Non ci sono migrazioni SQL aggiuntive da applicare dopo questo file.

### 4. Configurare la connessione al database

La connessione PDO usa il file locale:

```text
src/config/db.php
```

Questo file non viene versionato. Nel repository resta solo `src/config/db.example.php`, che serve da template per chi clona il progetto da zero.

Se `src/config/db.php` manca, crearlo una sola volta partendo dal template:

```powershell
Copy-Item src/config/db.example.php src/config/db.php
```

Configurazione locale predefinita nell'esempio:

```php
$host = 'localhost';
$port = '3306';
$dbname = 'nerdvault';
$username = 'root';
$password = '';
```

Questi valori sono adatti alla configurazione standard di XAMPP. Se il database
usa credenziali diverse, modificare `src/config/db.php` oppure impostare
`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME` e `DB_PASSWORD` nell'ambiente.

### 5. Configurare le email

La configurazione email usa il file locale:

```text
src/config/mail.php
```

Anche questo file non viene versionato. Nel repository resta solo `src/config/mail.example.php`, usato come template.

Se `src/config/mail.php` manca, crearlo una sola volta partendo dal template:

```powershell
Copy-Item src/config/mail.example.php src/config/mail.php
```

Il template parte con debug email attivo, quindi non richiede credenziali SMTP:
i link di verifica e reset vengono mostrati nell'app. Per usare Mailtrap impostare
`MAIL_DEBUG=0` e configurare le variabili `MAIL_HOST`, `MAIL_PORT`,
`MAIL_USERNAME` e `MAIL_PASSWORD`.

### 6. Configurare Apache

Per sicurezza, Apache deve servire solo la cartella `public`.

Aprire:

```text
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Aggiungere un virtual host:

```apache
<VirtualHost *:80>
    ServerName nerdvault.local
    DocumentRoot "C:/xampp/htdocs/WEB_APP/public"

    <Directory "C:/xampp/htdocs/WEB_APP/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 7. Configurare il file hosts (necessario per il virtual host)

Aprire come amministratore:

```text
C:\Windows\System32\drivers\etc\hosts
```

Aggiungere:

```text
127.0.0.1 nerdvault.local
```

### 8. Avviare l'applicazione

Avviare da XAMPP:

- Apache
- MySQL

Poi aprire nel browser:

```text
http://nerdvault.local
```

In alternativa, con il DocumentRoot standard di XAMPP e senza virtual host, e'
possibile usare:

```text
http://localhost/WEB_APP/public/
```

## Smarty

Smarty gestisce la parte View:

- `src/View/SmartyView.php` configura Smarty;
- `templates/` contiene i template `.tpl`;
- i controller renderizzano le pagine tramite `BaseController::view()`.

Per esempio, `UtenteController` renderizza `templates/utenti/login.tpl` tramite `SmartyView`.

## Routing

Il punto di ingresso pubblico e':

```text
public/index.php
```

Questo file avvia la sessione, carica Composer, apre la connessione al database e delega tutto a:

```text
src/Core/FrontController.php
```

La classe `App\Core\FrontController` normalizza la richiesta, rimuove eventuali prefissi locali come `/WEB_APP/public` e risolve il formato:

```text
/controller/action/parametri
```

Le rotte vengono lette dal percorso dell'URL, ad esempio `/annuncio/show/1`.
Apache inoltra le richieste a `public/index.php` tramite `public/.htaccess`;
query string e campi POST restano disponibili solo per filtri e dati dei form,
non per scegliere la route.

Esempi:

```text
/
/home/index
/annuncio/list
/annuncio/show/1
/auth/login
/auth/logout
/utente/profilo
/annuncio/show/1
/annuncio/create
```

## Flusso MVC

Il flusso principale dell'applicazione e':

```text
Browser
  -> public/index.php
  -> App\Core\FrontController
  -> middleware, se richiesto
  -> controller
  -> Foundation/FPersistentManager
  -> Foundation/F... table mapper
  -> Foundation/FDataBase
  -> database
  -> view
  -> HTML restituito al browser
```

Esempio per la lista annunci:

```text
public/index.php
  -> FrontController::handle()
  -> AnnuncioController::lista()
  -> FPersistentManager::annunciAttivi()
  -> FAnnuncio::attivi()
  -> templates/annunci/lista.tpl
```

La stessa logica e' stata estesa ai flussi principali di utenti, indirizzi, wishlist, carrello, feedback, segnalazioni, moderazione e pagamenti. Le query SQL e le transazioni stanno nel package `App\Foundation`; i controller coordinano richiesta, validazioni e rendering. Le transazioni di acquisto usano ancora `FOR UPDATE`, ma la query vive in `FAnnuncio::findWithDetailsForUpdate()`.


## Note per lo sviluppo

- Usare sempre `public/index.php` come punto di ingresso.
- Non includere direttamente file dentro `src/` dal browser.
- Usare i servizi in `src/services/` per la logica applicativa e le query.
- Usare `e()` per stampare dati dinamici nelle viste.
- Gli upload degli utenti devono restare dentro `public/uploads/`.
- Prima di lavorare, aggiornare sempre il branch `develop`.

## Sicurezza delle sessioni

`App\Core\SessionManager` centralizza il ciclo di vita della sessione PHP:

- abilita `session.use_strict_mode` e accetta l'ID soltanto tramite cookie;
- imposta il cookie con `HttpOnly` e `SameSite=Lax`;
- abilita automaticamente `Secure` sulle richieste HTTPS;
- rigenera e sostituisce l'ID di sessione dopo ogni login riuscito;
- invalida sia i dati server-side sia il cookie durante il logout;
- termina le sessioni autenticate dopo 30 minuti di inattivita.

Il timeout puo essere modificato, in secondi, tramite la variabile di ambiente
`SESSION_IDLE_TIMEOUT`; il valore `0` disabilita la scadenza per inattivita.
Se l'applicazione e dietro un proxy HTTPS che non espone direttamente HTTPS a
PHP, impostare `SESSION_COOKIE_SECURE=1` nell'ambiente di produzione.

## Workflow Git consigliato

```bash
git checkout develop
git pull origin develop
git status
```

Dopo le modifiche:

```bash
git add .
git commit -m "Descrizione modifica"
git push origin develop
```

