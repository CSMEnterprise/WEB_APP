# Diagrammi UML - NerdVault

Diagrammi dell'architettura del progetto, ricavati direttamente dal codice in
`src/`. I sorgenti sono in formato [PlantUML](https://plantuml.com): sono la
versione "ufficiale" e vanno modificati al posto delle immagini, che si
rigenerano da questi file.

## Elenco

| Sorgente | Diagramma | Cosa mostra |
|----------|-----------|-------------|
| `architettura_generale.puml`   | Architettura MVC a livelli | Flusso di una richiesta dal browser al database attraverso i livelli Web server, Core, Controller, View e Model/Persistenza. |
| `controller_routing.puml`      | Routing e controller | Pipeline del `FrontController` (match route, metodo HTTP, CSRF, middleware, dispatch) e i controller applicativi con le relative action. |
| `foundation_class_diagram.puml`| Class diagram del layer Foundation | `FDataBase` (singleton PDO), `FPersistentManager` (facade), `FBaseTable` e i repository concreti, le Entity di dominio e le loro relazioni. |

## Corrispondenza con il codice

- **Core**: `src/Core/` (`FrontController`, `Request`, `SessionManager`, `Csrf`, `UploadedFile`).
- **Controller**: `src/controllers/` (`BaseController` + controller applicativi).
- **Middleware**: `src/middleware/` (funzioni `requireAuth`, `requireGuest`, `requireAdmin`, ...).
- **View**: `src/View/` (`SmartyView`, `ViewDataNormalizer`) + `templates/*.tpl`.
- **Model/Persistenza**: `src/Foundation/` (`FDataBase`, `FPersistentManager`, `FBaseTable`, repository `F*`) e `src/Entity/` (`EBaseEntity`, entity `E*`).

## Come generare le immagini

- **VS Code**: estensione "PlantUML" -> apri il `.puml` -> `Alt+D` per l'anteprima.
- **Online**: incolla il contenuto su <https://www.plantuml.com/plantuml>.
- **CLI** (richiede Java + plantuml.jar):

  ```bash
  java -jar plantuml.jar INFO/UML/*.puml          # genera PNG
  java -jar plantuml.jar -tsvg INFO/UML/*.puml    # genera SVG
  ```

> I file `*.png` presenti nella cartella sono la vecchia versione e vanno
> rigenerati dai `.puml` con uno dei metodi sopra.
