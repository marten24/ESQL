<?php

$host = 'localhost';
$dbname = '';
$username = 'root'; // Nome utente del database
$password = 'root'; // Password del database

try {

    // Crea una nuova connessione PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Imposta l'attributo per generare eccezioni in caso di errori
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // creazione database
    $createDatabaseQuery = "CREATE DATABASE IF NOT EXISTS ESQL";
    if ($pdo->exec($createDatabaseQuery) === false) {
        echo "Error creating database: " . $pdo->errorInfo()[2];
    }

    // seleziona il database appena creato
    $pdo->exec("USE ESQL");
    
    $createTableDocenteSP = "DROP PROCEDURE IF EXISTS `CreateTableDocente`;
 
    CREATE PROCEDURE `CreateTableDocente`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Docente (
            EMAIL VARCHAR(255) PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            cognome VARCHAR(255) NOT NULL,
            nome_dipartimento VARCHAR(255),
            nome_corso VARCHAR(255),
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;";
    
    $pdo->exec($createTableDocenteSP);
    $pdo->exec("CALL CreateTableDocente();");

    $createTableStudenteSP = "DROP PROCEDURE IF EXISTS `CreateTableStudente`;
 
    CREATE PROCEDURE `CreateTableStudente`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Studente (
            EMAIL VARCHAR(255) PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            cognome VARCHAR(255) NOT NULL,
            codice VARCHAR(16) NOT NULL,
            anno_immatricolazione INT,
            password VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END
    ;";
    $pdo->exec($createTableStudenteSP);
    $pdo->exec("CALL CreateTableStudente();");

    $createTableStudente_TelefonoSP = "DROP PROCEDURE IF EXISTS `CreateTableStudente_Telefono`;
  
    CREATE PROCEDURE `CreateTableStudente_Telefono`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Studente_Telefono (
            EMAIL VARCHAR(255),
            telefono VARCHAR(255),
            FOREIGN KEY (EMAIL) REFERENCES Studente(EMAIL) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END ;";

    $pdo->exec($createTableStudente_TelefonoSP);
    $pdo->exec("CALL CreateTableStudente_Telefono();");

    $createTableDocente_TelefonoSP = "DROP PROCEDURE IF EXISTS `CreateTableDocente_Telefono`;
  
    CREATE PROCEDURE `CreateTableDocente_Telefono`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Docente_Telefono (
            EMAIL VARCHAR(255),
            telefono VARCHAR(255),
            FOREIGN KEY (EMAIL) REFERENCES Docente(EMAIL) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;";

    $pdo->exec($createTableDocente_TelefonoSP);
    $pdo->exec("CALL CreateTableDocente_Telefono();");

    $trovaDocenteConEmailePasswordSP = "DROP PROCEDURE IF EXISTS `TrovaDocenteConEmailePassword`;
    
    CREATE PROCEDURE `TrovaDocenteConEmailePassword`(IN p_email VARCHAR(255), IN p_password VARCHAR(255))
    BEGIN
        SELECT * FROM Docente WHERE EMAIL = p_email AND password = p_password;
    END;";
 
    $pdo->exec($trovaDocenteConEmailePasswordSP);

    $trovaStudenteConEmailePasswordSP = "DROP PROCEDURE IF EXISTS `TrovaStudenteConEmailePassword`;
    
    CREATE PROCEDURE `TrovaStudenteConEmailePassword`(IN p_email VARCHAR(255), IN p_password VARCHAR(255))
    BEGIN
        SELECT * FROM Studente WHERE EMAIL = p_email AND password = p_password;
    END;";
   
    $pdo->exec($trovaStudenteConEmailePasswordSP);

    $inserisciTelefonoStudenteSP = "DROP PROCEDURE IF EXISTS `InserisciTelefonoStudente`;
    
    CREATE PROCEDURE `InserisciTelefonoStudente`(IN email VARCHAR(255), IN telefono VARCHAR(255))
    BEGIN
        INSERT INTO Studente_Telefono (EMAIL, telefono) VALUES (email, telefono);
    END;";
  
    $pdo->exec($inserisciTelefonoStudenteSP);

    $inserisciTelefonoDocenteSP = "DROP PROCEDURE IF EXISTS `InserisciTelefonoDocente`;
    
    CREATE PROCEDURE `InserisciTelefonoDocente`(IN email VARCHAR(255), IN telefono VARCHAR(255))
    BEGIN
        INSERT INTO Docente_Telefono (EMAIL, telefono) VALUES (email, telefono);
    END;";

    $pdo->exec($inserisciTelefonoDocenteSP);

    $inserisciDocenteSP = "DROP PROCEDURE IF EXISTS `InserisciDocente`;
    
    CREATE PROCEDURE `InserisciDocente`(IN email VARCHAR(255), IN nome VARCHAR(255), IN cognome VARCHAR(255), IN nome_dipartimento VARCHAR(255), IN nome_corso VARCHAR(255), IN password VARCHAR(255))
    BEGIN
        INSERT INTO Docente (EMAIL, nome, cognome, nome_dipartimento, nome_corso, password) VALUES (email, nome, cognome, nome_dipartimento, nome_corso, password);
    END;";
  
    $pdo->exec($inserisciDocenteSP);

    $inserisciStudenteSP = "DROP PROCEDURE IF EXISTS `InserisciStudente`;
    
    CREATE PROCEDURE `InserisciStudente`(IN email VARCHAR(255), IN nome VARCHAR(255), IN cognome VARCHAR(255), IN codice VARCHAR(16), IN anno_immatricolazione INT, IN password VARCHAR(255))
    BEGIN
        INSERT INTO Studente (EMAIL, nome, cognome, codice, anno_immatricolazione, password) VALUES (email, nome, cognome, codice, anno_immatricolazione, password);
    END;";

    $pdo->exec($inserisciStudenteSP);

    $verificaEmailEsistenteDocenteSP = "DROP PROCEDURE IF EXISTS `VerificaEmailEsistenteDocente`;

CREATE PROCEDURE `VerificaEmailEsistenteDocente`(IN emailParam VARCHAR(255))
BEGIN
    SELECT EMAIL FROM Docente WHERE EMAIL = emailParam;
END;";
  
    $pdo->exec($verificaEmailEsistenteDocenteSP);

    $verificaEmailEsistenteStudenteSP = "DROP PROCEDURE IF EXISTS `VerificaEmailEsistenteStudente`;
    
    CREATE PROCEDURE `VerificaEmailEsistenteStudente`(IN emailParam VARCHAR(255))
    BEGIN
        SELECT EMAIL FROM Studente WHERE EMAIL = emailParam;
    END;";
  
    $pdo->exec($verificaEmailEsistenteStudenteSP);

$createTableTestSP = "DROP PROCEDURE IF EXISTS `CreateTableTest`;
 
    CREATE PROCEDURE `CreateTableTest`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Test (
            titolo VARCHAR(255) PRIMARY KEY,
            data DATE,
            visualizza_risposte BOOLEAN,
            email VARCHAR(255),
            FOREIGN KEY (email) REFERENCES Docente(EMAIL)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;";

$pdo->exec($createTableTestSP);
$pdo->exec("CALL CreateTableTest();");

$CreateTableTest_FotoSP = "DROP PROCEDURE IF EXISTS `CreateTableTest_Foto`;
  
    CREATE PROCEDURE `CreateTableTest_Foto`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Test_Foto (
            TITOLO VARCHAR(255) PRIMARY KEY,
            foto BLOB,
            FOREIGN KEY (TITOLO) REFERENCES Test(TITOLO) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;";

$pdo->exec($CreateTableTest_FotoSP);
$pdo->exec("CALL CreateTableTest_Foto();");

$InserisciFotoTestSP = "
    DROP PROCEDURE IF EXISTS `InserisciFotoTest`;

    CREATE PROCEDURE `InserisciFotoTest`(IN TITOLO VARCHAR(255), IN foto BLOB)
    BEGIN
        INSERT INTO Test_Foto (TITOLO, foto) VALUES (TITOLO, foto);
    END;
";

$pdo->exec($InserisciFotoTestSP);

$createTabella_SQLSP = "DROP PROCEDURE IF EXISTS `CreateTabellaSQL`;
 
   CREATE PROCEDURE CreateTabellaSQL()
   BEGIN
     CREATE TABLE IF NOT EXISTS Tabella_SQL (
        NOME VARCHAR(255) PRIMARY KEY,
        data DATE NOT NULL,
        num_righe INTEGER NOT NULL,
        email_docente VARCHAR(255) NOT NULL,
        FOREIGN KEY (email_docente) REFERENCES Docente(email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   END ;";

$pdo->exec($createTabella_SQLSP);
$pdo->exec("CALL CreateTabellaSQL();");

$InserisciTabellaSQLSP = "
    DROP PROCEDURE IF EXISTS `InserisciTabellaSQL`;

    CREATE PROCEDURE `InserisciTabellaSQL`(IN NOME VARCHAR(255), IN data DATE, IN num_righe INTEGER, IN email_docente VARCHAR(255))
    BEGIN
        INSERT INTO Tabella_SQL (NOME, data, num_righe, email_docente) VALUES (NOME, data, num_righe, email_docente);
    END;
";

$pdo->exec($InserisciTabellaSQLSP);

$CreateTableAttributoSP = "
   DROP PROCEDURE IF EXISTS `CreateTableAttributo`;

   CREATE PROCEDURE `CreateTableAttributo`()
   BEGIN
     CREATE TABLE IF NOT EXISTS Attributo (
        ID INTEGER AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        tipo VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   END ;
";

$pdo->exec($CreateTableAttributoSP);
$pdo->exec("CALL CreateTableAttributo();");

$InserisciAttributoSP = "
   DROP PROCEDURE IF EXISTS `InserisciAttributo`;

   CREATE PROCEDURE `InserisciAttributo`(IN nome VARCHAR(255), IN tipo VARCHAR(255))
   BEGIN
        INSERT INTO Attributo (nome, tipo) VALUES (nome, tipo);
   END;
";

$pdo->exec($InserisciAttributoSP);

$CreateTabellaSQLAttributoSP = "DROP PROCEDURE IF EXISTS `CreateTabellaSQLAttributo`;
 
   CREATE PROCEDURE CreateTabellaSQLAttributo()
   BEGIN
     CREATE TABLE IF NOT EXISTS Tabella_SQL_Attributo (
        ID_ATTRIBUTO INTEGER,
        NOME_TABELLA VARCHAR(255),
         PRIMARY KEY (ID_ATTRIBUTO, NOME_TABELLA),
         FOREIGN KEY (ID_ATTRIBUTO) REFERENCES Attributo(id),
        FOREIGN KEY (NOME_TABELLA) REFERENCES Tabella_SQL(nome)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   END ;";

$pdo->exec($CreateTabellaSQLAttributoSP);
$pdo->exec("CALL CreateTabellaSQLAttributo();");

$InserisciTabellaSQLAttributoSP = "
    DROP PROCEDURE IF EXISTS `InserisciTabellaSQLAttributo`;

    CREATE PROCEDURE `InserisciTabellaSQLAttributo`(IN ID_ATTRIBUTO INTEGER, IN NOME_TABELLA VARCHAR(255))
    BEGIN
        INSERT INTO Tabella_SQL_Attributo (ID_ATTRIBUTO, NOME_TABELLA) VALUES (ID_ATTRIBUTO, NOME_TABELLA);
    END;
";

$pdo->exec($InserisciTabellaSQLAttributoSP);

$CreateTabellaAttributoChiavePrimariaSP = "DROP PROCEDURE IF EXISTS `CreateTabellaAttributoChiavePrimaria`;
 
   CREATE PROCEDURE CreateTabellaAttributoChiavePrimaria()
   BEGIN
     CREATE TABLE IF NOT EXISTS Attributo_Chiave_Primaria (
        ID_ATTRIBUTO INTEGER PRIMARY KEY,
        chiave_primaria BOOLEAN,
         FOREIGN KEY (ID_ATTRIBUTO) REFERENCES Attributo(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   END ;";

$pdo->exec($CreateTabellaAttributoChiavePrimariaSP);
$pdo->exec("CALL CreateTabellaAttributoChiavePrimaria();");

$InserisciAttributoChiavePrimariaSP = "
    DROP PROCEDURE IF EXISTS `InserisciAttributoChiavePrimaria`;

    CREATE PROCEDURE `InserisciAttributoChiavePrimaria`(IN ID_ATTRIBUTO INTEGER, IN chiave_primaria BOOLEAN)
    BEGIN
        INSERT INTO Attributo_Chiave_Primaria (ID_ATTRIBUTO, chiave_primaria) VALUES (ID_ATTRIBUTO, chiave_primaria);
    END;
";

$pdo->exec($InserisciAttributoChiavePrimariaSP);


$createTableQuesitoSP = "DROP PROCEDURE IF EXISTS `CreateTableQuesito`;
 
  CREATE PROCEDURE `CreateTableQuesito`()
  BEGIN
    CREATE TABLE IF NOT EXISTS Quesito (
        numero INT,
        titolo VARCHAR(255),
        difficolta ENUM('Basso', 'Medio', 'Alto'),
        num_risposte INT,
        descrizione TEXT,
        PRIMARY KEY (numero, titolo),
        FOREIGN KEY (titolo) REFERENCES Test(titolo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;";

    $pdo->exec($createTableQuesitoSP);
    $pdo->exec("CALL CreateTableQuesito();");

    $createTableQuesito_ChiusoSP = "DROP PROCEDURE IF EXISTS `CreateTableQuesito_Chiuso`;

CREATE PROCEDURE `CreateTableQuesito_Chiuso`()
BEGIN
    CREATE TABLE IF NOT EXISTS Quesito_Chiuso (
        numero INT,
        titolo VARCHAR(255),
        PRIMARY KEY (numero, titolo),
        FOREIGN KEY (numero) REFERENCES Quesito(numero),
        FOREIGN KEY (titolo) REFERENCES Test(titolo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";
    $pdo->exec($createTableQuesito_ChiusoSP);
    $pdo->exec("CALL CreateTableQuesito_Chiuso();");
   
$visualizzaTabella_SQLSP = "DROP PROCEDURE IF EXISTS `VisualizzaTabelleSQL`;
 
 CREATE PROCEDURE VisualizzaTabelleSQL(IN parametro_email_docente VARCHAR(255))
BEGIN
    SELECT * FROM Tabella_SQL WHERE email_docente = parametro_email_docente;
END;";
$pdo->exec($visualizzaTabella_SQLSP);

$visualizzaAttributiTabellaSQL_SP = "DROP PROCEDURE IF EXISTS `VisualizzaAttributiTabellaSQL`;
CREATE PROCEDURE VisualizzaAttributiTabellaSQL(IN nome_tabella VARCHAR(255))
BEGIN
    SET @query = CONCAT('SHOW COLUMNS FROM ', nome_tabella);
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END;";

$pdo->exec($visualizzaAttributiTabellaSQL_SP);

$visualizzaChiaviPrimarieTabellaSQL_SP = "DROP PROCEDURE IF EXISTS `VisualizzaChiaviPrimarieTabellaSQL`;
 
 CREATE PROCEDURE VisualizzaChiaviPrimarieTabellaSQL(IN nome_tabella VARCHAR(255))
BEGIN
    DECLARE chiavi_primarie VARCHAR(255);

    SELECT GROUP_CONCAT(COLUMN_NAME) INTO chiavi_primarie
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_NAME = nome_tabella
    AND CONSTRAINT_NAME = 'PRIMARY';

    SELECT chiavi_primarie AS ChiaviPrimarie;
END;
";

$pdo->exec($visualizzaChiaviPrimarieTabellaSQL_SP);

$CreateTabellaAttributoVincoloIntegritaSP = "DROP PROCEDURE IF EXISTS `CreateTabellaAttributoVincoloIntegrita`;
 
   CREATE PROCEDURE CreateTabellaAttributoVincoloIntegrita()
   BEGIN
     CREATE TABLE IF NOT EXISTS Attributo_Vincolo_Integrita (
        ID INTEGER AUTO_INCREMENT PRIMARY KEY,
        id_attributo INTEGER,
        chiave_esterna BOOLEAN,
     FOREIGN KEY (id_attributo) REFERENCES Attributo(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   END ;";

$pdo->exec($CreateTabellaAttributoVincoloIntegritaSP);
$pdo->exec("CALL CreateTabellaAttributoVincoloIntegrita();");

$InserisciAttributoVincoloIntegritaSP = "
    DROP PROCEDURE IF EXISTS `InserisciAttributoVincoloIntegrita`;

    CREATE PROCEDURE `InserisciAttributoVincoloIntegrita`(IN id_attributo INTEGER, IN chiave_esterna BOOLEAN)
    BEGIN
        INSERT INTO Attributo_Vincolo_integrita (id_attributo, chiave_esterna) VALUES (id_attributo, chiave_esterna);
    END;
";

$pdo->exec($InserisciAttributoVincoloIntegritaSP);

$createTabellaSQLEsercizioSP = "DROP PROCEDURE IF EXISTS `CreateTabellaSQLEsercizio`;
 
  CREATE PROCEDURE CreateTabellaSQLEsercizio(nomeTabella VARCHAR(255))
BEGIN
    SET @createQuery = CONCAT('CREATE TABLE ', nomeTabella, ' (
                            id INT PRIMARY KEY AUTO_INCREMENT
                          )');
    PREPARE createStmt FROM @createQuery;
    EXECUTE createStmt;
    DEALLOCATE PREPARE createStmt;
END;";

$pdo->exec($createTabellaSQLEsercizioSP);

$aggiungiColonnaSP = "DROP PROCEDURE IF EXISTS `AggiungiColonna`;
 
  CREATE PROCEDURE AggiungiColonna (
    IN nomeTabella VARCHAR(255),
    IN attributoNome VARCHAR(255),
    IN attributoTipo VARCHAR(255)
)
BEGIN
    SET @alterQuery = CONCAT('ALTER TABLE ', nomeTabella, ' ADD COLUMN ', attributoNome, ' ', attributoTipo);
    PREPARE alterStmt FROM @alterQuery;
    EXECUTE alterStmt;
    DEALLOCATE PREPARE alterStmt;
END";

$pdo->exec($aggiungiColonnaSP);

$aggiungiChiavePrimariaSQL = "DROP PROCEDURE IF EXISTS `aggiungiChiavePrimariaSQL`;
 
CREATE PROCEDURE aggiungiChiavePrimariaSQL(
    IN nomeTabella VARCHAR(255),
    IN valoriChiaviPrimarie VARCHAR(255),
    IN attributoNome VARCHAR(255)
)
BEGIN
    SET @addPrimaryKeySQL = CONCAT('ALTER TABLE ', nomeTabella, ' DROP PRIMARY KEY, ADD PRIMARY KEY (', valoriChiaviPrimarie, ',', attributoNome, ')');
    PREPARE addPrimaryKeyStmt FROM @addPrimaryKeySQL;
    EXECUTE addPrimaryKeyStmt;
    DEALLOCATE PREPARE addPrimaryKeyStmt;
END";

$pdo->exec($aggiungiChiavePrimariaSQL);

$aggiungiVincoloUnicita = "DROP PROCEDURE IF EXISTS `AggiungiVincoloUnicita`;
 
CREATE PROCEDURE AggiungiVincoloUnicita(
    IN nomeTabella VARCHAR(255),
    IN attributoNome VARCHAR(255)
)
BEGIN
    SET @addUniqueConstraintSQL = CONCAT('ALTER TABLE ', nomeTabella, ' ADD UNIQUE (', attributoNome, ')');
    PREPARE addUniqueConstraintStmt FROM @addUniqueConstraintSQL;
    EXECUTE addUniqueConstraintStmt;
    DEALLOCATE PREPARE addUniqueConstraintStmt;
END";

$pdo->exec($aggiungiVincoloUnicita);


$aggiungiChiaveEsterna = "DROP PROCEDURE IF EXISTS `AggiungiChiaveEsterna`;
 
CREATE PROCEDURE AggiungiChiaveEsterna(
    IN nomeTabellaCreata VARCHAR(255),
    IN nomeTabella VARCHAR(255),
    IN attributoNome VARCHAR(255),
    IN attributoTipo VARCHAR(255),
    IN attributoChiaveEsterna VARCHAR(255)
)
BEGIN
    SET @alterTableQuery = CONCAT('ALTER TABLE ', nomeTabellaCreata, '
                                    MODIFY COLUMN ', attributoNome, ' ', attributoTipo, ',
                                    ADD CONSTRAINT fk_', attributoNome, '
                                    FOREIGN KEY (', attributoNome, ') REFERENCES ', nomeTabella, '(', attributoChiaveEsterna, ')');
    PREPARE alterTableStmt FROM @alterTableQuery;
    EXECUTE alterTableStmt;
    DEALLOCATE PREPARE alterTableStmt;
END";

$pdo->exec($aggiungiChiaveEsterna);

$GetForeignKeys = "DROP PROCEDURE IF EXISTS `GetForeignKeys`;
 
CREATE PROCEDURE GetForeignKeys(
    IN nomeTabella VARCHAR(255)
)
BEGIN
    SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_NAME = CONCAT('Tabella_di_Esercizio_', nomeTabella) AND CONSTRAINT_NAME <> 'PRIMARY';
END";

$pdo->exec($GetForeignKeys);

$inserisciTestSP = "
    DROP PROCEDURE IF EXISTS `InserisciTest`;

    CREATE PROCEDURE `InserisciTest`(IN titolo VARCHAR(255), IN data DATE, IN visualizza_risposte BOOLEAN, IN email VARCHAR(255))
    BEGIN
        INSERT INTO Test (titolo, data, visualizza_risposte, email) VALUES (titolo, data, visualizza_risposte, email);
    END;
";

$pdo->exec($inserisciTestSP);

$inserisciFotoTestSP = "
    DROP PROCEDURE IF EXISTS `InserisciFotoTest`;

    CREATE PROCEDURE `InserisciFotoTest`(IN titolo VARCHAR(255), IN foto LONGBLOB)
    BEGIN
        INSERT INTO Test_foto (titolo, foto) VALUES (titolo, foto);
    END;
";

$pdo->exec($inserisciFotoTestSP);

$inserisciQuesitoSP = "
  DROP PROCEDURE IF EXISTS `InserisciQuesito`;
CREATE PROCEDURE `InserisciQuesito`(
    IN p_test VARCHAR(255),
    IN p_difficolta ENUM('Basso', 'Medio', 'Alto'),
    IN p_num_risposte INT,
    IN p_descrizione TEXT, 
    OUT p_numero INT
    
)
BEGIN
    -- Trova il massimo numero di quesito per il test specificato
    SET @max_numero = (SELECT IFNULL(MAX(numero), 0) FROM Quesito WHERE titolo = p_test FOR UPDATE);

    -- Incrementa di 1 il numero per il nuovo quesito
    SET @new_numero = @max_numero + 1;

    -- Inserisce il nuovo quesito con il numero calcolato
    INSERT INTO Quesito (numero, titolo, difficolta, num_risposte, descrizione)
    VALUES (@new_numero, p_test, p_difficolta, p_num_risposte, p_descrizione);

    SET p_numero = @new_numero; -- Restituisce il numero del nuovo quesito
    
END ;";

$pdo->exec($inserisciQuesitoSP);

$createTableQuesito_CodiceSP = "DROP PROCEDURE IF EXISTS `CreateTableQuesito_Codice`;

CREATE PROCEDURE `CreateTableQuesito_Codice`()
BEGIN
    CREATE TABLE IF NOT EXISTS Quesito_Codice (
        numero INT,
        titolo VARCHAR(255),
        PRIMARY KEY (numero, titolo),
        FOREIGN KEY (numero) REFERENCES Quesito(numero),
        FOREIGN KEY (titolo) REFERENCES Test(titolo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";

$pdo->exec($createTableQuesito_CodiceSP);
$pdo->exec("CALL CreateTableQuesito_Codice();");

$inserisciQuesitoChiusoSP = "
DROP PROCEDURE IF EXISTS `InserisciQuesitoChiuso`;
CREATE PROCEDURE `InserisciQuesitoChiuso`(
    IN p_numero INT,
    IN p_titolo VARCHAR(255)
)
BEGIN
    INSERT INTO Quesito_Chiuso (numero, titolo) VALUES (p_numero, p_titolo);
END;";
$pdo->exec($inserisciQuesitoChiusoSP);

$inserisciQuesitoCodiceSP = "
DROP PROCEDURE IF EXISTS `InserisciQuesitoCodice`;
CREATE PROCEDURE `InserisciQuesitoCodice`(
    IN p_numero INT,
    IN p_titolo VARCHAR(255)
)
BEGIN
    INSERT INTO Quesito_Codice (numero, titolo) VALUES (p_numero, p_titolo);
END;";
$pdo->exec($inserisciQuesitoCodiceSP);

$inserisciSoluzioneSP = "
DROP PROCEDURE IF EXISTS `InserisciSoluzione`;
CREATE PROCEDURE `InserisciSoluzione`(
    IN p_numero INT,
    IN p_titolo VARCHAR(255),
    IN p_testo TEXT
)
BEGIN
    INSERT INTO Soluzione (numero, titolo, testo) VALUES (p_numero, p_titolo, p_testo);
END;";

$pdo->exec($inserisciSoluzioneSP);

$inserisciQuesitoChiusoOpzioneSP = "
DROP PROCEDURE IF EXISTS `InserisciQuesitoChiusoOpzione`;
CREATE PROCEDURE `InserisciQuesitoChiusoOpzione`(
     IN p_id INT,
    IN p_numero INT,
    IN p_titolo VARCHAR(255)
)
BEGIN
    INSERT INTO Quesito_Chiuso_Opzione (id, numero, titolo) VALUES (p_id, p_numero, p_titolo);
END;";

$pdo->exec($inserisciQuesitoChiusoOpzioneSP);

$inserisciMessaggioSP = "
DROP PROCEDURE IF EXISTS `InserisciMessaggio`;
CREATE PROCEDURE `InserisciMessaggio`(
    IN p_titolo_messaggio VARCHAR(255),
    IN p_testo TEXT,
    IN p_data DATE,
    IN p_titolo VARCHAR(255),
    IN p_email_docente VARCHAR(255)
)
BEGIN
    INSERT
    INTO Messaggio (titolo_messaggio, testo, data, titolo, email_docente)
    VALUES (p_titolo_messaggio, p_testo, p_data, p_titolo, p_email_docente);
END;";

$pdo->exec($inserisciMessaggioSP);

$titoliTestSP = "
DROP PROCEDURE IF EXISTS `GetTitoliTest`;
CREATE PROCEDURE `GetTitoliTest`()
BEGIN
    SELECT TITOLO FROM Test;
END;";

$pdo->exec($titoliTestSP);

$messaggiDocenteSP = "
DROP PROCEDURE IF EXISTS `GetMessaggiDocente`;
CREATE PROCEDURE `GetMessaggiDocente`(
    IN p_email_docente VARCHAR(255)
)
BEGIN
    SELECT * FROM Messaggio WHERE email_studente IS NOT NULL AND email_docente = p_email_docente;
END;";

$pdo->exec($messaggiDocenteSP);

$emailDocenteTestSP = "
DROP PROCEDURE IF EXISTS `GetEmailDocenteByTitolo`;
CREATE PROCEDURE `GetEmailDocenteByTitolo`(
    IN p_titolo VARCHAR(255)
)
BEGIN
    SELECT email FROM Test WHERE titolo = p_titolo;
END;";

$pdo->exec($emailDocenteTestSP);

$inserisciMessaggioStudenteSP = "
DROP PROCEDURE IF EXISTS `InserisciMessaggioStudente`;
CREATE PROCEDURE `InserisciMessaggioStudente`(
    IN p_titolo_messaggio VARCHAR(255),
    IN p_testo TEXT,
    IN p_data DATE,
    IN p_titolo VARCHAR(255),
    IN p_email_studente VARCHAR(255),
    IN p_email_docente VARCHAR(255)
)
BEGIN
    INSERT
    INTO Messaggio (titolo_messaggio, testo, data, titolo, email_studente, email_docente)
    VALUES (p_titolo_messaggio, p_testo, p_data, p_titolo, p_email_studente, p_email_docente);
END;";

$pdo->exec($inserisciMessaggioStudenteSP);

$createTableOpzioneSP = "DROP PROCEDURE IF EXISTS `CreateTableOpzione`;

CREATE PROCEDURE `CreateTableOpzione`()
BEGIN
    CREATE TABLE IF NOT EXISTS Opzione (
        id INT AUTO_INCREMENT,
        numero INT,
        titolo VARCHAR(255),
        testo TEXT,
        PRIMARY KEY (id, numero, titolo),
        FOREIGN KEY (numero) REFERENCES Quesito_Chiuso(numero),
        FOREIGN KEY (titolo) REFERENCES Quesito_Chiuso(titolo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";

$pdo->exec($createTableOpzioneSP);
$pdo->exec("CALL CreateTableOpzione();");

$inserisciOpzioneSP = "
DROP PROCEDURE IF EXISTS `InserisciOpzione`;
CREATE PROCEDURE `InserisciOpzione`(
    IN p_numero INT,
    IN p_titolo VARCHAR(255),
    IN p_testo TEXT
)
BEGIN
    INSERT INTO Opzione (numero, titolo, testo)
    VALUES (p_numero, p_titolo, p_testo);
END;";
$pdo->exec($inserisciOpzioneSP);

$numeroOpzioneSP = "
DROP PROCEDURE IF EXISTS `GetMaxIdOpzione`;
CREATE PROCEDURE `GetMaxIdOpzione`()
BEGIN
    SELECT MAX(id) AS id FROM Opzione;
END;";
$pdo->exec($numeroOpzioneSP);

$createTableQuesito_Chiuso_OpzioneSP = "DROP PROCEDURE IF EXISTS `CreateTableQuesito_Chiuso_Opzione`;

CREATE PROCEDURE `CreateTableQuesito_Chiuso_Opzione`()
BEGIN
    CREATE TABLE IF NOT EXISTS Quesito_Chiuso_Opzione (
        numero INT,
        titolo VARCHAR(255),
        id INT,
        PRIMARY KEY (numero, titolo, id),
        FOREIGN KEY (numero) REFERENCES Quesito_Chiuso(numero),
        FOREIGN KEY (titolo) REFERENCES Quesito_Chiuso(titolo),
        FOREIGN KEY (id) REFERENCES Opzione(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";

$pdo->exec($createTableQuesito_Chiuso_OpzioneSP);
$pdo->exec("CALL CreateTableQuesito_Chiuso_Opzione();");

$createTableSoluzioneSP = "DROP PROCEDURE IF EXISTS `CreateTableSoluzione`;

CREATE PROCEDURE `CreateTableSoluzione`()
BEGIN
    CREATE TABLE IF NOT EXISTS Soluzione (
        id INT AUTO_INCREMENT PRIMARY KEY,
        testo TEXT,
        numero INT,
        titolo VARCHAR(255),
        FOREIGN KEY (numero) REFERENCES Quesito(numero),
        FOREIGN KEY (titolo) REFERENCES Test(titolo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";
    
$pdo->exec($createTableSoluzioneSP);
$pdo->exec("CALL CreateTableSoluzione();");

$createTableMessaggioSP = "DROP PROCEDURE IF EXISTS `CreateTableMessaggio`;

CREATE PROCEDURE `CreateTableMessaggio`()
BEGIN
    CREATE TABLE IF NOT EXISTS Messaggio (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titolo_messaggio VARCHAR(255),
        testo TEXT,
        data DATE,
        titolo VARCHAR(255),
        email_studente VARCHAR(255),
        email_docente VARCHAR(255),
        FOREIGN KEY (titolo) REFERENCES Test(titolo),
        FOREIGN KEY (email_studente) REFERENCES Studente(EMAIL),
        FOREIGN KEY (email_docente) REFERENCES Docente(EMAIL)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;";

$pdo->exec($createTableMessaggioSP);
$pdo->exec("CALL CreateTableMessaggio();");

$titoliVisualizzaRisposteDocenteSP = "
DROP PROCEDURE IF EXISTS `GetTitoliVisualizzaRisposteDocente`;
CREATE PROCEDURE `GetTitoliVisualizzaRisposteDocente`(
    IN p_email_docente VARCHAR(255)
)
BEGIN
    SELECT titolo, visualizza_risposte FROM Test WHERE email = p_email_docente;
END;";

$pdo->exec($titoliVisualizzaRisposteDocenteSP);

$visualizzaRisposteSP = "
DROP PROCEDURE IF EXISTS `GetVisualizzaRisposte`;
CREATE PROCEDURE `GetVisualizzaRisposte`(
    IN p_titolo VARCHAR(255)
)
BEGIN
    SELECT visualizza_risposte
    FROM Test
    WHERE titolo = p_titolo;
END;";

$pdo->exec($visualizzaRisposteSP);

$aggiornaVisualizzaRisposteSP = "
DROP PROCEDURE IF EXISTS `AggiornaVisualizzaRisposte`;
CREATE PROCEDURE `AggiornaVisualizzaRisposte`(
    IN p_titolo VARCHAR(255),
    IN p_nuovo_stato BOOLEAN
)
BEGIN
    UPDATE Test
    SET visualizza_risposte = p_nuovo_stato
    WHERE titolo = p_titolo;
END;";

$pdo->exec($aggiornaVisualizzaRisposteSP);

$titoliVisualizzaRisposteSP = "
DROP PROCEDURE IF EXISTS `GetTitoliVisualizzaRisposte`;
CREATE PROCEDURE `GetTitoliVisualizzaRisposte`()
BEGIN
    SELECT titolo, visualizza_risposte FROM Test;
END;";

$pdo->exec($titoliVisualizzaRisposteSP);

$ritornaNumRighe_SP = "DROP PROCEDURE IF EXISTS ritornaNumRighe;

CREATE PROCEDURE ritornaNumRighe(IN nomeTabella VARCHAR(255))
BEGIN
    SELECT num_righe FROM tabella_sql WHERE nome = nomeTabella;
END;";

$pdo->exec($ritornaNumRighe_SP);

$visualizzaTabella_SQLSP = "DROP PROCEDURE IF EXISTS VisualizzaTabellaSQL;
 
CREATE PROCEDURE VisualizzaTabellaSQL(IN email VARCHAR(255))
BEGIN
    SELECT * FROM Tabella_SQL WHERE email_docente = email;
END;";

$pdo->exec($visualizzaTabella_SQLSP);

$pdo->exec("DROP PROCEDURE IF EXISTS `CreateTableRisposta`;");
$CreateTableRisposta = "
    CREATE PROCEDURE `CreateTableRisposta`()
    BEGIN
   CREATE TABLE IF NOT EXISTS `Risposta` (
        `idRisposta` INT AUTO_INCREMENT PRIMARY KEY,
        `userEmail` VARCHAR(255) NOT NULL,
        `numeroQuesito` INT NOT NULL,
        `titolo` VARCHAR(255) NULL,
        `esito` BOOLEAN NULL,
        FOREIGN KEY (`userEmail`) REFERENCES `Studente`(`EMAIL`) ON DELETE CASCADE,
        FOREIGN KEY (`numeroQuesito`, `titolo`) REFERENCES `Quesito`(`numero`, `titolo`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;
";

$pdo->exec($CreateTableRisposta);
$pdo->exec("CALL CreateTableRisposta();");

$tipoUtenteSP = "
CREATE PROCEDURE `GetTipoUtente`(IN emailParam VARCHAR(255))
BEGIN
    SELECT 'Studente' AS tipo FROM Studente WHERE email = emailParam
    UNION
    SELECT 'Docente' AS tipo FROM Docente WHERE email = emailParam;
END";
$pdo->exec("DROP PROCEDURE IF EXISTS `GetTipoUtente`;");
$pdo->exec($tipoUtenteSP);
    
$messaggiStudenteSP = "CREATE PROCEDURE `GetMessaggiStudente`(
    IN p_email_studente VARCHAR(255)
)
BEGIN
    SELECT id, titolo, titolo_messaggio, testo, data, email_docente FROM Messaggio WHERE (email_docente IS NOT NULL) AND (email_studente IS NULL);
END;";
$pdo->exec("DROP PROCEDURE IF EXISTS `GetMessaggiStudente`;");
$pdo->exec($messaggiStudenteSP);

$quesitoByTitoloSP = "CREATE PROCEDURE `GetQuesitoByTitolo`(
    IN p_titolo VARCHAR(255)
)
BEGIN
    SELECT * FROM Quesito WHERE titolo = p_titolo;
END;";
$pdo->exec("DROP PROCEDURE IF EXISTS `GetQuesitoByTitolo`;");
$pdo->exec($quesitoByTitoloSP);

    $testSP = "CREATE PROCEDURE `GetTest`()
BEGIN
    SELECT Test.*, Test_Foto.foto
    FROM Test
    LEFT JOIN Test_Foto ON Test.titolo = Test_Foto.TITOLO;
END;";
$pdo->exec("DROP PROCEDURE IF EXISTS `GetTest`;");
$pdo->exec($testSP);

    $createViewSQL = "CREATE OR REPLACE VIEW ClassificaQuesiti AS
                      SELECT numero, titolo, difficolta, num_risposte, descrizione
                      FROM Quesito
                      ORDER BY num_risposte DESC";

    $pdo->exec($createViewSQL);

    $visualizzaClassificaQuesitiSP = "CREATE PROCEDURE `VisualizzaClassificaQuesiti`()
BEGIN
    SELECT * FROM ClassificaQuesiti;
END;";
    $pdo->exec("DROP PROCEDURE IF EXISTS `VisualizzaClassificaQuesiti`;");
    $pdo->exec($visualizzaClassificaQuesitiSP);
 

    $pdo->exec("DROP PROCEDURE IF EXISTS `CreateStudenteTestTable`;");
$createStudenteTestTable = "
CREATE PROCEDURE `CreateStudenteTestTable`()
    BEGIN
        CREATE TABLE IF NOT EXISTS Studente_test (
            email VARCHAR(255),
            titolo VARCHAR(255),
            stato ENUM('Aperto', 'InCompletamento', 'Concluso') NOT NULL DEFAULT 'Aperto',
            data_prima_risposta DATETIME NULL,
            data_ultima_risposta DATETIME NULL,
            PRIMARY KEY (email, titolo),
            FOREIGN KEY (email) REFERENCES Studente(EMAIL) ON DELETE CASCADE,
            FOREIGN KEY (titolo) REFERENCES Test(titolo) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;   
";

$pdo->exec($createStudenteTestTable);
$pdo->exec("CALL CreateStudenteTestTable();");

$pdo->exec("DROP PROCEDURE IF EXISTS `CreateTableRispostaChiusa`;");
$CreateTableRispostaChiusa = "
 CREATE PROCEDURE `CreateTableRispostaChiusa`()
BEGIN
     CREATE TABLE IF NOT EXISTS `RispostaChiusa` (
        `idOpzione` INT NOT NULL,
        `idRisposta` INT,
        `userEmail` VARCHAR(255) NOT NULL,
        `numeroQuesito` INT,
        `titolo` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`idOpzione`, `userEmail`),
        FOREIGN KEY (`idRisposta`) REFERENCES `Risposta`(`idRisposta`) ON DELETE CASCADE,
        FOREIGN KEY (`idOpzione`) REFERENCES `Opzione`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`numeroQuesito`, `titolo`) REFERENCES `Quesito`(`numero`, `titolo`) ON DELETE CASCADE,
        FOREIGN KEY (`userEmail`) REFERENCES `Studente`(`EMAIL`) ON DELETE CASCADE,
        FOREIGN KEY (`titolo`) REFERENCES `Test`(`titolo`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
END;
";

$pdo->exec($CreateTableRispostaChiusa);
$pdo->exec("CALL CreateTableRispostaChiusa();");

$pdo->exec("DROP PROCEDURE IF EXISTS `CreateTableRispostaCodice`;");
$CreateTableRispostaCodice = "
    CREATE PROCEDURE `CreateTableRispostaCodice`()
    BEGIN
         CREATE TABLE IF NOT EXISTS `RispostaCodice` (
            `idRisposta` INT NOT NULL,
            `userEmail` VARCHAR(255) NOT NULL,
            `numeroQuesito` INT NOT NULL,
            `testoRisposta` TEXT NOT NULL,
            PRIMARY KEY (`idRisposta`),
            FOREIGN KEY (`numeroQuesito`) REFERENCES `Quesito`(`NUMERO`) ON DELETE CASCADE,
            FOREIGN KEY (`userEmail`) REFERENCES `Studente`(`EMAIL`) ON DELETE CASCADE,
            FOREIGN KEY (`idRisposta`) REFERENCES `Risposta`(`idRisposta`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END;
";
$pdo->exec($CreateTableRispostaCodice);
$pdo->exec("CALL CreateTableRispostaCodice();");


$createTriggerUpdateRisposta = "
   CREATE TRIGGER before_insert_risposta
BEFORE INSERT ON Risposta
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM Studente_test 
        WHERE email = NEW.userEmail AND titolo = NEW.titolo
    ) THEN
        INSERT INTO Studente_test (email, titolo, stato, data_prima_risposta) 
        VALUES (NEW.userEmail, NEW.titolo, 'InCompletamento', NOW());
    ELSE
        UPDATE Studente_test 
        SET data_ultima_risposta = NOW() 
        WHERE email = NEW.userEmail AND titolo = NEW.titolo;
    END IF;
END;
";
    $pdo->exec("DROP TRIGGER IF EXISTS before_insert_risposta;");
    $pdo->exec($createTriggerUpdateRisposta);

$createTriggerConclusoRisposta = "
CREATE TRIGGER after_insert_risposta
AFTER UPDATE ON Risposta
FOR EACH ROW
BEGIN
    DECLARE totQuesiti INT;
    DECLARE risposteCorrette INT;
    DECLARE risposteTotali INT;

    SELECT COUNT(*) INTO totQuesiti
    FROM Quesito
    WHERE titolo = NEW.titolo;

    SELECT COUNT(*) INTO risposteCorrette
    FROM Risposta
    WHERE userEmail = NEW.userEmail AND titolo = NEW.titolo AND esito = 1;

    SELECT COUNT(*) INTO risposteTotali
    FROM Risposta
    WHERE userEmail = NEW.userEmail AND titolo = NEW.titolo;

    IF totQuesiti = risposteTotali AND totQuesiti = risposteCorrette THEN
        UPDATE Studente_test
        SET stato = 'Concluso', data_ultima_risposta = NOW()
        WHERE email = NEW.userEmail AND titolo = NEW.titolo;
    END IF;
END;
";

    $pdo->exec("DROP TRIGGER IF EXISTS after_insert_risposta;");
    $pdo->exec($createTriggerConclusoRisposta);
    
    $createTriggerVisualizzaRisposte = "
CREATE TRIGGER UpdateStudenteTestStato AFTER UPDATE ON Test
FOR EACH ROW
BEGIN
    IF NEW.visualizza_risposte = TRUE THEN
        UPDATE Studente_Test
        SET stato = 'Concluso' -- o lo stato desiderato
        WHERE titolo = NEW.titolo;
    END IF;
END";
    $pdo->exec("DROP TRIGGER IF EXISTS UpdateStudenteTestStato;");
    $pdo->exec($createTriggerVisualizzaRisposte);
   
$getStatoTest_SP = "
    DROP PROCEDURE IF EXISTS `GetStatoTest`;

    CREATE PROCEDURE GetStatoTest(IN emailParam VARCHAR(255), IN titoloParam VARCHAR(255))
BEGIN
    SELECT stato FROM Studente_test WHERE email = emailParam AND titolo = titoloParam;
END;
";

$pdo->exec($getStatoTest_SP);


$creaVerificaRispostaChiusa_SP = "
    DROP PROCEDURE IF EXISTS `VerificaRispostaChiusa`;

    CREATE PROCEDURE VerificaRispostaChiusa(
    IN userEmailParam VARCHAR(255),
    IN numeroQuesitoParam INT,
    IN idOpzioneParam INT
)
BEGIN
    SELECT COUNT(*)
    FROM RispostaChiusa
    WHERE userEmail = userEmailParam 
    AND numeroQuesito = numeroQuesitoParam
    AND idOpzione = idOpzioneParam;
END;
";
$pdo->exec($creaVerificaRispostaChiusa_SP);

$creaInserisciRisposta_SP = "
    DROP PROCEDURE IF EXISTS `InserisciRisposta`;

   CREATE PROCEDURE InserisciRisposta(
    IN userEmailParam VARCHAR(255),
    IN numeroQuesitoParam INT
    )
    BEGIN
    INSERT INTO Risposta (userEmail, numeroQuesito)
    VALUES (userEmailParam, numeroQuesitoParam);
    END;
";
$pdo->exec($creaInserisciRisposta_SP);

    $creaInserisciRispostaChiusa_SP = "
   DROP PROCEDURE IF EXISTS `InserisciRispostaChiusa`;

CREATE PROCEDURE `InserisciRispostaChiusa`(IN idRispostaParam INT, IN userEmailParam VARCHAR(255), IN numeroQuesitoParam INT, IN idOpzioneParam INT, IN titoloParam VARCHAR(255))
BEGIN
    DECLARE existingEntries INT;

    SELECT COUNT(*) INTO existingEntries
    FROM RispostaChiusa
    WHERE idRisposta = idRispostaParam AND userEmail = userEmailParam AND numeroQuesito = numeroQuesitoParam AND idOpzione = idOpzioneParam AND titolo = titoloParam;

    IF existingEntries = 0 THEN
        INSERT INTO RispostaChiusa (idRisposta, userEmail, numeroQuesito, idOpzione, titolo)
        VALUES (idRispostaParam, userEmailParam, numeroQuesitoParam, idOpzioneParam, titoloParam);
    END IF;
END;
";
    $pdo->exec($creaInserisciRispostaChiusa_SP);

    $creaEliminaRispostaCodice_SP = "
    DROP PROCEDURE IF EXISTS `EliminaRispostaCodice`;
    
CREATE PROCEDURE EliminaRispostaCodice(
    IN userEmailParam VARCHAR(255),
    IN numeroQuesitoParam INT
)
BEGIN
    DELETE FROM RispostaCodice
    WHERE userEmail = userEmailParam AND numeroQuesito = numeroQuesitoParam;
END;
";
$pdo->exec($creaEliminaRispostaCodice_SP);

$creaOttieniIdRisposta_SP = "
    DROP PROCEDURE IF EXISTS `OttieniIdRisposta`;

    CREATE PROCEDURE `OttieniIdRisposta`(IN `emailParam` VARCHAR(255), IN `quesitoNumParam` INT)
    BEGIN
        SELECT idRisposta FROM Risposta WHERE userEmail = emailParam AND numeroQuesito = quesitoNumParam;
    END;
";
$pdo->exec($creaOttieniIdRisposta_SP);

$creaInserisciRispostaCodice_SP = "
    DROP PROCEDURE IF EXISTS `InserisciRispostaCodice`;

    CREATE PROCEDURE `InserisciRispostaCodice`(IN `idRispostaParam` INT, IN `emailParam` VARCHAR(255), IN `quesitoNumParam` INT, IN `testoRispostaParam` TEXT)
    BEGIN
        INSERT INTO RispostaCodice (idRisposta, userEmail, numeroQuesito, testoRisposta) VALUES (idRispostaParam, emailParam, quesitoNumParam, testoRispostaParam);
    END;
";
$pdo->exec($creaInserisciRispostaCodice_SP);

$creaRecuperaRisposteCodice_SP = "
    DROP PROCEDURE IF EXISTS `RecuperaRisposteCodice`;

    CREATE PROCEDURE `RecuperaRisposteCodice`(IN `emailParam` VARCHAR(255), IN `testIdParam` VARCHAR(255))
    BEGIN
        SELECT 
            RC.idRisposta, RC.userEmail, RC.numeroQuesito, RC.testoRisposta
        FROM 
            RispostaCodice RC
            JOIN Quesito QC ON RC.numeroQuesito = QC.NUMERO
        WHERE 
            RC.userEmail = emailParam AND QC.TITOLO = testIdParam;
    END;
";
$pdo->exec($creaRecuperaRisposteCodice_SP);


$creaRecuperaRisposteCodice_SP = "
    DROP PROCEDURE IF EXISTS `RecuperaRisposteCodice`;

    CREATE PROCEDURE `RecuperaRisposteCodice`(IN `emailParam` VARCHAR(255), IN `testIdParam` VARCHAR(255))
    BEGIN
        SELECT 
            RC.idRisposta, RC.userEmail, RC.numeroQuesito, RC.testoRisposta
        FROM 
            RispostaCodice RC
            JOIN Quesito QC ON RC.numeroQuesito = QC.NUMERO
        WHERE 
            RC.userEmail = emailParam AND QC.TITOLO = testIdParam;
    END;
";
$pdo->exec($creaRecuperaRisposteCodice_SP);


$creaRecuperaSoluzioni_SP = "
    DROP PROCEDURE IF EXISTS `RecuperaSoluzioni`;

    CREATE PROCEDURE `RecuperaSoluzioni`(IN `numeroQuesitoParam` INT, IN `titoloTestParam` VARCHAR(255))
    BEGIN
        SELECT S.testo
        FROM Soluzione S
        WHERE S.numero = numeroQuesitoParam AND S.titolo = titoloTestParam;
    END;
";
$pdo->exec($creaRecuperaSoluzioni_SP);

$creaRecuperaRisposteChiuse_SP = "
    DROP PROCEDURE IF EXISTS `GetRispostaChiusa`;

CREATE PROCEDURE `GetRispostaChiusa`(IN userEmailParam VARCHAR(255), IN titoloParam VARCHAR(255))
BEGIN
    SELECT R.idRisposta, R.userEmail, R.idOpzione, QC.id
    FROM RispostaChiusa R
    JOIN Quesito_Chiuso_Opzione QC ON R.numeroQuesito = QC.numero AND R.titolo = QC.titolo
    WHERE R.userEmail = userEmailParam AND QC.titolo = titoloParam AND R.idOpzione IS NOT NULL;
END;
";
$pdo->exec($creaRecuperaRisposteChiuse_SP);

$creaUpdateEsito_SP = "
    DROP PROCEDURE IF EXISTS `UpdateEsito`;

CREATE PROCEDURE `UpdateEsito`(IN esitoParam INT, IN idRispostaParam INT)
BEGIN
    UPDATE Risposta SET esito = esitoParam WHERE idRisposta = idRispostaParam;
END;
";
$pdo->exec($creaUpdateEsito_SP);

    $createView2SQL = "CREATE OR REPLACE VIEW ClassificaStudenti AS
SELECT 
    s.codice AS CodiceStudente,
    COUNT(r.idRisposta) AS TotaleRisposte,
    SUM(CASE WHEN r.esito = '1' THEN 1 ELSE 0 END) AS RisposteCorrette,
    (SUM(CASE WHEN r.esito = '1' THEN 1 ELSE 0 END) / COUNT(r.idRisposta)) AS PercentualeCorrette
FROM 
    Studente s
LEFT JOIN 
    Risposta r ON s.email = r.userEmail
GROUP BY 
    s.codice
ORDER BY 
    PercentualeCorrette DESC;
";
    $pdo->exec($createView2SQL);

    $visualizzaClassificaStudentiSP = "CREATE PROCEDURE `VisualizzaClassificaStudenti`()
BEGIN
    SELECT * FROM ClassificaStudenti;
END;";
    $pdo->exec("DROP PROCEDURE IF EXISTS `VisualizzaClassificaStudenti`;");
    $pdo->exec($visualizzaClassificaStudentiSP);

$GetQuesitiSP = "DROP PROCEDURE IF EXISTS `GetQuesiti`;
CREATE PROCEDURE `GetQuesiti`(IN titoloTestParam VARCHAR(255))
BEGIN
    SELECT Q.NUMERO, Q.TITOLO, Q.DIFFICOLTA, Q.NUM_RISPOSTE, Q.DESCRIZIONE, IF(QC.NUMERO IS NULL, 'codice', 'chiuso') AS tipo
    FROM Quesito Q
    LEFT JOIN Quesito_Chiuso QC ON Q.NUMERO = QC.NUMERO AND Q.TITOLO = QC.TITOLO
    WHERE Q.TITOLO = titoloTestParam;
END;";
$pdo->exec($GetQuesitiSP);

    $GetRisposteCodice = "DROP PROCEDURE IF EXISTS `GetRisposteCodice`;

CREATE PROCEDURE `GetRisposteCodice`(IN userEmailParam VARCHAR(255), IN titoloParam VARCHAR(255))
BEGIN
    SELECT RC.idRisposta, RC.userEmail, RC.numeroQuesito, RC.testoRisposta
    FROM RispostaCodice RC
    JOIN Quesito_Codice QC ON RC.numeroQuesito = QC.numero
    WHERE RC.userEmail = userEmailParam AND QC.titolo = titoloParam;
END;";
    $pdo->exec($GetRisposteCodice);

    $visualizzaClassificaQuesitiSP =
        "DROP PROCEDURE IF EXISTS `VisualizzaClassificaQuesiti`;
        CREATE PROCEDURE VisualizzaClassificaQuesiti()
BEGIN
    SELECT 
        Q.numero, 
        Q.titolo, 
        COUNT(R.idRisposta) AS NumeroRisposte
    FROM 
        Quesito Q
    LEFT JOIN 
        Risposta R ON Q.numero = R.numeroQuesito
    GROUP BY 
        Q.numero, Q.titolo
    ORDER BY 
        NumeroRisposte DESC;
END;";
    $pdo->exec("DROP PROCEDURE IF EXISTS `VisualizzaClassficaQuesiti`;");
    $pdo->exec($visualizzaClassificaQuesitiSP);

    $VisualizzaClassificaStudentiTestCompletatiSP =
    "CREATE OR REPLACE VIEW ClassificaStudentiTestCompletati AS
SELECT 
    s.codice AS CodiceStudente,
    COUNT(st.titolo) AS NumeroTestCompletati
FROM 
    Studente s
JOIN 
    Studente_test st ON s.email = st.email
WHERE 
    st.stato = 'Concluso'
GROUP BY 
    s.codice
ORDER BY 
    NumeroTestCompletati DESC, s.codice;";
    $pdo->exec("DROP PROCEDURE IF EXISTS `VisualizzaClassificaStudentiTestCompletati`;");
    $pdo->exec( $VisualizzaClassificaStudentiTestCompletatiSP);

    $getTitoloSP = "
    DROP PROCEDURE IF EXISTS `GetTitolo`;

    CREATE PROCEDURE GetTitolo(IN p_titolo VARCHAR(255))
    BEGIN
        SELECT TITOLO FROM Quesito WHERE titolo = p_titolo;
    END;
";
    $pdo->exec($getTitoloSP);


    $verificaEsistenzaRispostaSP = "
    DROP PROCEDURE IF EXISTS `VerificaEsistenzaRisposta`;

    CREATE PROCEDURE VerificaEsistenzaRisposta(IN p_userEmail VARCHAR(255), IN p_numeroQuesito INT, IN p_titolo VARCHAR(255))
    BEGIN
        SELECT COUNT(*) FROM Risposta WHERE userEmail = p_userEmail AND numeroQuesito = p_numeroQuesito AND titolo = p_titolo;
    END;
";
    $pdo->exec($verificaEsistenzaRispostaSP);
  
  $getTestPhotoSP = "
    DROP PROCEDURE IF EXISTS `GetTestPhoto`;

    CREATE PROCEDURE GetTestPhoto(IN testTitle VARCHAR(255))
    BEGIN
        SELECT foto FROM Test_Foto WHERE TITOLO = testTitle;
    END;
";
    $pdo->exec($getTestPhotoSP);

    $creaEliminaRisposta_SP = "
    DROP PROCEDURE IF EXISTS `EliminaRisposta`;

CREATE PROCEDURE `EliminaRisposta`(IN userEmailParam VARCHAR(255), IN numeroQuesitoParam INT)
BEGIN
    DELETE FROM Risposta WHERE userEmail = userEmailParam AND numeroQuesito = numeroQuesitoParam;
END;
";
    $pdo->exec($creaEliminaRisposta_SP);

    $creaGetSoluzioni_SP = "
    DROP PROCEDURE IF EXISTS `GetSoluzioni`;

CREATE PROCEDURE `GetSoluzioni`(IN numeroParam INT, IN titoloParam VARCHAR(255))
BEGIN
    SELECT S.testo
    FROM Soluzione S
    WHERE S.numero = numeroParam AND S.titolo = titoloParam;
END;
";
    $pdo->exec($creaGetSoluzioni_SP);

    $GetTestTitolo_SP = "
    DROP PROCEDURE IF EXISTS `GetTestTitolo`;
    CREATE PROCEDURE GetTestTitolo()
BEGIN
    SELECT TITOLO FROM Test;
END;
";
 $pdo->exec($GetTestTitolo_SP);

    
} catch (PDOException $e) {
    error_log("Errore durante la creazione delle tabelle: " . $e->getMessage());
    die("Errore durante la creazione delle tabelle");
}

// Chiudi la connessione se non necessaria in seguito
$pdo = null;
?>
