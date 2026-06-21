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
|   |-- categorie.txt
|   |-- Struttura DB.txt
|   |-- ToDo WEB_APP.txt
|   `-- altri file di documentazione
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
- `INFO/`: contiene documentazione, appunti, schema DB e materiali del progetto.

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

```bash
cp src/config/db.example.php src/config/db.php
```

Configurazione locale predefinita nell'esempio:

```php
$host = 'localhost';
$port = '3306';
$dbname = 'nerdvault';
$username = 'root';
$password = '';
```

Questi valori sono adatti alla configurazione standard di XAMPP. Se il database usa credenziali diverse, modificarle direttamente in `src/config/db.php`.

### 5. Configurare le email

La configurazione email usa il file locale:

```text
src/config/mail.php
```

Anche questo file non viene versionato. Nel repository resta solo `src/config/mail.example.php`, usato come template.

Se `src/config/mail.php` manca, crearlo una sola volta partendo dal template:

```bash
cp src/config/mail.example.php src/config/mail.php
```

Con `debug` impostato a `true`, i link di verifica email e reset password vengono salvati in sessione invece di essere inviati tramite SMTP.

### 6. Configurare Apache

Per sicurezza, Apache deve servire solo la cartella `public`.

Aprire:

```text
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Aggiungere un virtual host:

```apache
<VirtualHost *:80>
    ServerName web_app.local
    DocumentRoot "C:/xampp/htdocs/WEB_APP/public"

    <Directory "C:/xampp/htdocs/WEB_APP/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 7. Configurare il file hosts(opzionale)

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

In alternativa, se non si configura un virtual host, e' possibile usare:

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

