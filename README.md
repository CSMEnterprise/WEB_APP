# NerdVault - WEB_APP

NerdVault e' una web app marketplace sviluppata in PHP per la gestione di annunci, utenti, wishlist, carrello, pagamenti simulati, account business, feedback, segnalazioni e area amministratore.

Il progetto usa una struttura MVC leggera senza framework esterni: `public/index.php` fa solo da bootstrap, mentre il routing principale e' gestito dalla classe `App\Controllers\FrontController`. Controller, servizi, entity, viste, middleware e configurazione sono separati nella cartella `src`.

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
|   |   `-- db.php
|   |-- controllers/
|   |-- Entity/
|   |-- Foundation/
|   |-- helpers/
|   |-- middleware/
|   |-- services/
|   |-- templates/
|   `-- views/
|       |-- admin/
|       |-- annunci/
|       |-- auth/
|       |-- business/
|       |-- carrello/
|       |-- errors/
|       |-- feedback/
|       |-- layout/
|       |-- pagamenti/
|       |-- partials/
|       |-- segnalazioni/
|       |-- utenti/
|       `-- wishlist/
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
- `public/css/`: contiene gli stylesheet dell'interfaccia, separati dalle view PHP e dai template Smarty.
- `public/uploads/`: contiene i file caricati dagli utenti, per esempio le immagini degli annunci.
- `src/config/`: contiene la configurazione dell'applicazione, inclusa la connessione al database.
- `src/controllers/`: riceve le richieste dal router e coordina servizi e viste.
- `src/Entity/`: contiene le classi Entity con proprieta private, getter, setter e metodi di utilita.
- `src/Foundation/`: contiene classi infrastrutturali, per esempio il renderer Smarty.
- `src/services/`: contiene la logica applicativa e le query al database tramite PDO.
- `src/templates/`: contiene i template Smarty introdotti gradualmente.
- `src/views/`: contiene le pagine PHP ancora renderizzate direttamente dall'applicazione.
- `src/middleware/`: contiene i controlli di accesso per utenti autenticati, admin, business e guest.
- `src/helpers/`: contiene funzioni comuni, come l'escape HTML.
- `database/`: contiene lo schema SQL completo per creare il database.
- `INFO/`: contiene documentazione, appunti, schema DB e materiali del progetto.

## Namespace e autoload

Il progetto usa Composer anche per caricare le classi interne.

Namespace principali:

- `App\Controllers\` -> `src/controllers/`
- `App\Services\` -> `src/services/`
- `App\Entity\` -> `src/Entity/`
- `App\Foundation\` -> `src/Foundation/`

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

Il branch di lavoro del progetto e' `Develop`:

```bash
git checkout Develop
git pull origin Develop
```

### 3. Importare il database

1. Avviare MySQL/MariaDB da XAMPP.
2. Aprire phpMyAdmin.
3. Importare il file:

```text
database/nerdvault.sql
```

Il file crea automaticamente il database `nerdvault`, tutte le tabelle, le chiavi, i vincoli e le categorie di base.

Se esiste gia un vecchio database `nerdvault` e vuoi ricrearlo da zero, esporta prima un backup e poi elimina il vecchio database da phpMyAdmin prima di importare questo file.

Non ci sono migrazioni SQL aggiuntive da applicare dopo questo file.

### 4. Configurare la connessione al database

La connessione PDO e' configurata in:

```text
src/config/db.php
```

Configurazione locale predefinita:

```php
$host = 'localhost';
$port = '3306';
$dbname = 'nerdvault';
$username = 'root';
$password = '';
```

Questi valori sono adatti alla configurazione standard di XAMPP. Se il database usa credenziali diverse, modificarle in quel file.

### 5. Configurare Apache

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

### 6. Configurare il file hosts

Aprire come amministratore:

```text
C:\Windows\System32\drivers\etc\hosts
```

Aggiungere:

```text
127.0.0.1 web_app.local
```

### 7. Avviare l'applicazione

Avviare da XAMPP:

- Apache
- MySQL

Poi aprire nel browser:

```text
http://web_app.local
```

In alternativa, se non si configura un virtual host, e' possibile usare:

```text
http://localhost/WEB_APP/public/
```

## Smarty

Smarty e' stato introdotto in modo graduale nella parte View:

- `src/Foundation/SmartyView.php` configura Smarty;
- `src/templates/` contiene i nuovi template `.tpl`;
- `src/views/` resta disponibile per le pagine PHP non ancora convertite.

La prima pagina convertita e' il login: `UtenteController` renderizza `src/templates/utenti/login.tpl` tramite `SmartyView`.

## Routing

Il punto di ingresso pubblico e':

```text
public/index.php
```

Questo file avvia la sessione, carica Composer, apre la connessione al database e delega tutto a:

```text
src/controllers/FrontController.php
```

La classe `App\Controllers\FrontController` normalizza la rotta e contiene lo `switch` principale dell'applicazione.

Le rotte vengono lette da:

- query string: `index.php?route=annunci`
- campi hidden nei form POST: `<input name="route" value="...">`
- `PATH_INFO`, se configurato da Apache

Esempi:

```text
index.php?route=home
index.php?route=annunci
index.php?route=annuncio&id=1
index.php?route=login
index.php?route=register
index.php?route=carrello
index.php?route=wishlist
index.php?route=admin
```

## Flusso MVC

Il flusso principale dell'applicazione e':

```text
Browser
  -> public/index.php
  -> App\Controllers\FrontController
  -> middleware, se richiesto
  -> controller
  -> service
  -> database
  -> view
  -> HTML restituito al browser
```

Esempio per la lista annunci:

```text
public/index.php
  -> FrontController::handle()
  -> AnnuncioController::lista()
  -> AnnuncioService::getAnnunciAttivi()
  -> src/views/annunci/lista.php
```

## Verifica sintassi PHP

Con XAMPP su Windows e' possibile controllare la sintassi dei file PHP con:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```

## Note per lo sviluppo

- Usare sempre `public/index.php` come punto di ingresso.
- Non includere direttamente file dentro `src/` dal browser.
- Usare i servizi in `src/services/` per la logica applicativa e le query.
- Usare `e()` per stampare dati dinamici nelle viste.
- Gli upload degli utenti devono restare dentro `public/uploads/`.
- Prima di lavorare, aggiornare sempre il branch `Develop`.

## Workflow Git consigliato

```bash
git checkout Develop
git pull origin Develop
git status
```

Dopo le modifiche:

```bash
git add .
git commit -m "Descrizione modifica"
git push origin Develop
```

## Avvertenze

Il progetto e' configurato per ambiente locale XAMPP. Prima di un eventuale deploy online e' consigliato:

- disattivare `APP_DEBUG` in produzione;
- spostare le credenziali database fuori dal codice versionato;
- aggiungere protezione CSRF ai form e alle azioni che modificano dati;
- evitare azioni distruttive via GET;
- non versionare file caricati dagli utenti o file generati automaticamente.
