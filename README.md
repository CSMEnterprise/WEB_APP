# Web-App
Web-App per la gestione marketplace sviluppata in PHP con Apache/XAMPP.
## Tecnologie
- PHP 8.x
- Apache
- MySQL
- HTML5, CSS3, JavaScript
- XAMPP per ambiente locale
Ecco la versione aggiornata del README con l’indicazione chiara di lavorare sempre sul branch `develop`:

---

# Configurazione ambiente locale con XAMPP e Apache

Questa sezione descrive come configurare l’ambiente di sviluppo locale per eseguire la web-app utilizzando **XAMPP** e **Apache**.

---

# 1. Clonare la repository

Clonare la repository nella cartella `htdocs` di XAMPP.

Percorso tipico su Windows:

```bash
C:\xampp\htdocs\
```

Eseguire:

```bash
git clone https://github.com/CSMEnterprise/WEB_APP.git
```

La struttura risultante sarà simile alla seguente:

```
htdocs
└── WEB_APP
    ├── public
    ├── src
    ├── config
    ├── database
    ├── assets
    ├── README.md
    └── .github
```

---

# 2. Struttura del progetto

Il progetto utilizza una struttura che separa il codice interno dai file accessibili dal browser.

```
WEB_APP
│
├── public
│   ├── index.php
│   ├── css
│   ├── js
│   └── images
│
├── src
│   ├── controllers
│   ├── models
│   └── services
│
├── config
│   └── database.php
│
├── database
│   └── schema.sql
│
├── assets
│
└── README.md
```

### Descrizione cartelle

* **public/** → File accessibili dal browser (entry point dell’applicazione).
* **src/** → Codice sorgente dell’applicazione (controller, modelli, logica).
* **config/** → File di configurazione dell’applicazione (database, impostazioni).
* **database/** → Script SQL per creare il database.
* **assets/** → Risorse statiche (CSS, JS, immagini).

---

# 3. Lavorare sempre sul branch `develop`

Tutte le modifiche devono essere effettuate **sul branch `develop`**.
Il branch `main` rimane stabile e contiene solo il codice verificato pronto per la produzione.

**Flusso consigliato:**

```bash
# passare a develop
git checkout Develop

# aggiornare il branch locale
git pull origin Develop
```

---

# 4. Configurare Apache (Virtual Host)

Per motivi di sicurezza e organizzazione, Apache deve servire **solo la cartella `public`**.

Aprire il file:

```
xampp/apache/conf/extra/httpd-vhosts.conf
```

e aggiungere:

```
<VirtualHost *:80>
    ServerName web_app.local
    DocumentRoot "C:/xampp/htdocs/WEB_APP/public"

    <Directory "C:/xampp/htdocs/WEB_APP/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

# 5. Configurare il file hosts

Aprire:

```
C:\Windows\System32\drivers\etc\hosts
```

e aggiungere:

```
127.0.0.1 WEB_APP.local
```

---

# 6. Avviare il server

Aprire il pannello XAMPP e avviare:

* Apache
* MySQL (se necessario)

---

# 7. Avviare l'applicazione

Aprire il browser:

```
http://WEB_APP.local
```

Apache utilizzerà automaticamente:

```
WEB_APP/public/index.php
```

come punto di ingresso.

---

# 8. Workflow di sviluppo

1. **Aggiornare il branch develop**:

```bash
git checkout Develop
git pull origin Develop
```

2. **Effettuare modifiche** su `Develop`.

3. **Commit e push delle modifiche**:

```bash
git add .
git commit -m "descrizione modifiche"
git push origin Develop
```

4. Le modifiche vengono poi integrate nel branch `main` tramite Pull Request.

---

# 9. Note

I file come:

* `README.md`
* `.github/`
* `.git/`

sono utilizzati per la gestione del progetto e della repository e **non interferiscono con il funzionamento del server Apache**.

---

Se vuoi, posso anche creare **una sezione finale con “Buone pratiche Git” per il team**, così tutti i membri sapranno esattamente come gestire branch, feature e Pull Request. Vuoi che lo faccia?
