# ESQL – Piattaforma per esercitazioni SQL

Progetto del corso di **Basi di Dati – A.A. 2023/2024**.

ESQL è un'applicazione web che permette ai docenti di creare tabelle di esercizio, test e quesiti sul linguaggio SQL, e agli studenti di svolgerli ricevendo una valutazione automatica delle risposte. L'applicazione si appoggia al database relazionale **ESQLDB**, progettato a partire dalla specifica fino all'implementazione in MySQL.

## Funzionalità

### Per tutti gli utenti
- Registrazione e login come **docente** o **studente**
- Visualizzazione dei test disponibili e dei relativi quesiti
- Consultazione delle statistiche della piattaforma

### Docente
- **Gestione tabelle di esercizio**: creazione di tabelle con attributi (nome, tipo, chiave primaria, chiave esterna verso altre tabelle), inserimento e rimozione di righe
- **Creazione di test** con titolo univoco, data ed eventuale foto
- **Creazione di quesiti** con livello di difficoltà (Basso, Medio, Alto), collegati a una o più tabelle di esercizio:
  - *quesiti a risposta chiusa*, con un insieme di opzioni
  - *quesiti di codice*, con una o più soluzioni SQL di riferimento
- **Gestione visibilità risposte**: abilitando la visualizzazione delle risposte di un test, i test degli studenti vengono conclusi automaticamente tramite trigger
- **Messaggistica**: invio di messaggi relativi a un test, ricevuti da tutti gli studenti

### Studente
- Svolgimento dei test, con possibilità di inviare più risposte per lo stesso quesito finché il test non è concluso
- Per i quesiti di codice: editor della query con **anteprima del risultato** prima dell'invio (le tabelle si richiamano con il nome breve, senza prefisso, limitando l'accesso alle sole tabelle consentite)
- **Correzione automatica**: una risposta chiusa è corretta se coincide con l'opzione del docente, una risposta di codice se produce lo stesso output della soluzione
- Pagina dei risultati con esito per quesito e percentuale di risposte corrette (verde sopra il 60%, rosso sotto)
- Invio di messaggi al docente che ha creato un test e inbox dei messaggi ricevuti

### Statistiche
Implementate come viste SQL e visibili a tutti; per privacy gli studenti compaiono solo con il loro codice alfanumerico:
- Classifica degli studenti per numero di test conclusi
- Classifica degli studenti per percentuale di risposte corrette
- Classifica dei quesiti per numero di risposte ricevute

## Progettazione del database

Il progetto segue l'intero percorso di progettazione di una base di dati:

1. **Analisi dei requisiti**: decomposizione della specifica, glossario dei termini, operazioni sui dati e business rules
2. **Progettazione concettuale**: schema ER con le generalizzazioni Utente → Docente/Studente, Quesito → Quesito chiuso/di codice, Risposta → Risposta chiusa/di codice
3. **Analisi delle ridondanze**: valutazione del campo `num_risposte` del quesito sulla base della tabella dei volumi e delle frequenze delle operazioni. La ridondanza è stata mantenuta perché riduce il costo complessivo delle operazioni di circa 2,8 volte con un overhead di memoria trascurabile
4. **Progettazione logica**: ristrutturazione dello schema, vincoli di chiave e vincoli di integrità referenziale
5. **Normalizzazione**: verifica delle dipendenze funzionali
6. **Implementazione** in MySQL con trigger e viste

### Trigger principali
- `before_insert_risposta`: alla prima risposta crea il record di svolgimento del test con stato `InCompletamento`, alle successive aggiorna la data dell'ultima risposta
- `after_insert_risposta`: imposta lo stato del test a `Concluso` quando lo studente ha risposto correttamente a tutti i quesiti
- `UpdateStudenteTestStato`: conclude i test di tutti gli studenti quando il docente rende visibili le risposte

### Viste
- `classificaquesiti`
- `classificastudenti`
- `classificastudentitestcompletati`

## Tecnologie

- **PHP 8.2**
- **MySQL 5.7** (gestito con phpMyAdmin)
- **HTML, CSS, JavaScript**
- Ambiente di sviluppo locale MAMP

## Installazione

1. Clonare il repository nella cartella del web server locale (es. `htdocs` di MAMP/XAMPP)
2. Creare il database `ESQL` e importare il dump SQL incluso nel progetto tramite phpMyAdmin
3. Aggiornare le credenziali di connessione al database nei file PHP, se diverse da quelle predefinite
4. Avviare il server e aprire `index.php` dal browser

## Documentazione

La relazione completa del progetto (specifica, schema ER, schema logico, analisi delle ridondanze e codice SQL) è disponibile nel file `relazione.pdf`.

## Autori

- Marco Tenace @marten24
- Eugenio De Rosa @EugenioDeRosa
