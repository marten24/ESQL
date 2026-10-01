<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}

$userEmail = $_SESSION['user_email'];
$testId = isset($_GET['testId']) ? $_GET['testId'] : '';
$messaggiErrore = [];

$stmtStatoTest = $pdo->prepare("SELECT stato FROM Studente_test WHERE email = ? AND titolo = ?");
$stmtStatoTest->execute([$userEmail, $testId]);
$statoTest = $stmtStatoTest->fetchColumn();

$fotoTest = null;
$stmtFoto = $pdo->prepare("CALL GetTestPhoto(?)");
$stmtFoto->execute([$testId]);
$fotoData = $stmtFoto->fetchColumn();
if ($fotoData !== false) {
    $fotoTest = 'data:image/jpeg;base64,' . base64_encode($fotoData);
}
$stmtFoto->closeCursor();

$risultatiQuery = []; // Array per memorizzare i risultati delle query per ciascun quesito

// Gestione dell'invio delle risposte dei quesiti chiusi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $statoTest !== 'Concluso') {
    if (isset($_POST['risposta'])) {
    foreach ($_POST['risposta'] as $numeroQuesito => $idOpzioni) {
        foreach ($idOpzioni as $idOpzione) {
            $stmtTitoloTest = $pdo->prepare("CALL GetTitolo(?)");
            $stmtTitoloTest->execute([$testId]);
            $titoloTest = $stmtTitoloTest->fetchColumn();
            $stmtTitoloTest->closeCursor();
            // Verifica se esiste già una risposta di quell'utente
            $stmtVerificaEsistenzaRisposta = $pdo->prepare("CALL VerificaEsistenzaRisposta(?, ?, ?)");
            $stmtVerificaEsistenzaRisposta->execute([$userEmail, $numeroQuesito, $titoloTest]);
            $esisteRisposta = $stmtVerificaEsistenzaRisposta->fetchColumn() == 0;
            $stmtVerificaEsistenzaRisposta->closeCursor();
            $stmt = $pdo->prepare("INSERT INTO Risposta (userEmail, numeroQuesito, titolo) VALUES (?, ?, ?)");
            $stmt->execute([$userEmail, $numeroQuesito, $titoloTest]);
            $stmt->closeCursor();
            $idRisposta = $pdo->lastInsertId();
            if ($esisteRisposta) {
                // Incrementa num_risposte in Quesito solo se la risposta è nuova
                $stmtIncrementa = $pdo->prepare("UPDATE Quesito SET num_risposte = num_risposte + 1 WHERE numero = ? AND titolo = ?");
                $stmtIncrementa->execute([$numeroQuesito, $titoloTest]);
            }
            // Verifica e inserisce la risposta chiusa se non esiste già
            $stmtVerificaDuplicato = $pdo->prepare("SELECT COUNT(*) FROM RispostaChiusa WHERE userEmail = ? AND numeroQuesito = ? AND idOpzione = ? AND titolo = ?");
            $stmtVerificaDuplicato->execute([$userEmail, $numeroQuesito, $idOpzione, $titoloTest]);
            if ($stmtVerificaDuplicato->fetchColumn() == 0) {
               // $stmtVerificaDuplicato->closeCursor();
                $stmtInserisciRispostaChiusa = $pdo->prepare("CALL InserisciRispostaChiusa( ?, ?, ?, ?, ?)");
                $stmtInserisciRispostaChiusa->execute([$idRisposta, $userEmail, $numeroQuesito, $idOpzione, $titoloTest]);
            } else {
                        $messaggiErrore[] = "Hai già inviato una risposta per l'opzione: $idOpzione del quesito: $numeroQuesito.";
                    }
             }
        }
    }
}
// Gestione dell'invio delle risposte dei quesiti di codice
if (isset($_POST['rispostaCodice'])) {
    foreach ($_POST['rispostaCodice'] as $numeroQuesito => $testoRisposta) {
        // Prima elimina le risposte precedenti in RispostaCodice
        $stmtEliminaRispostaCodice = $pdo->prepare("CALL EliminaRispostaCodice(?, ?)");
        $stmtEliminaRispostaCodice->execute([$userEmail, $numeroQuesito]);
        // Elimina risposta in Risposta
        $stmtEliminaRisposta = $pdo->prepare("CALL EliminaRisposta(?, ?)");
        $stmtEliminaRisposta->execute([$userEmail, $numeroQuesito]);
        $stmtTitoloTest = $pdo->prepare("CALL GetTitolo(?)");
        $stmtTitoloTest->execute([$testId]);
        $titoloTest = $stmtTitoloTest->fetchColumn();
        $stmtTitoloTest->closeCursor();
        // Verifica se esiste già una risposta di quell'utente
        $stmtVerificaEsistenzaRisposta = $pdo->prepare("CALL VerificaEsistenzaRisposta(?, ?, ?)");
        $stmtVerificaEsistenzaRisposta->execute([$userEmail, $numeroQuesito, $titoloTest]);
        $esisteRisposta = $stmtVerificaEsistenzaRisposta->fetchColumn() == 0;
        $stmtVerificaEsistenzaRisposta->closeCursor();
        // Inserisce la risposta solo se non esiste già
        $stmt = $pdo->prepare("INSERT INTO Risposta (userEmail, numeroQuesito,titolo) VALUES ( ?, ?, ?)");
        $stmt->execute([$userEmail, $numeroQuesito,$titoloTest]);
        // Ottiene l'ID dell'ultima inserzione
        $idRisposta = $pdo->lastInsertId();
        // Verifica se esiste già una risposta per lo stesso utente e quesito

        if ($esisteRisposta) {
            // Incrementa num_risposte in Quesito solo se la risposta è nuova
            $stmtIncrementa = $pdo->prepare("UPDATE Quesito SET num_risposte = num_risposte + 1 WHERE numero = ? AND titolo = ?");
            $stmtIncrementa->execute([$numeroQuesito, $titoloTest]);
        }

        // Inserisce la nuova risposta in RispostaCodice utilizzando l'idRisposta ottenuto
        $stmtInserisci = $pdo->prepare("CALL InserisciRispostaCodice(?, ?, ?, ?)");
        $stmtInserisci->execute([$idRisposta, $userEmail, $numeroQuesito, $testoRisposta]);

    }
}
//modifico la query in modo da evitare che si possa aggirare le tabelle e che siano correttamente formalizzate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rispostaCodice'])) {
    foreach ($_POST['rispostaCodice'] as $numeroQuesito => $testoRisposta) {
        if (preg_match('/^\s*SELECT\s+/i', $testoRisposta)) {
            if (preg_match('/FROM\s+([a-zA-Z0-9_]+)/i', $testoRisposta, $matches)) {
                $nomeTabellaUtente = $matches[1];
                $nomeTabellaCompleto = "ESQL.Tabella_di_Esercizio_" . $nomeTabellaUtente;
                $testoRispostaCorretto = str_ireplace("FROM " . $nomeTabellaUtente, "FROM " . $nomeTabellaCompleto, $testoRisposta);
                try {
                    $stmtEsercizio = $pdo->prepare($testoRispostaCorretto);
                    $stmtEsercizio->execute();
                    $risultatiEsercizio = $stmtEsercizio->fetchAll(PDO::FETCH_ASSOC);
                    $risultatiQuery[$numeroQuesito] = $risultatiEsercizio;
                } catch (PDOException $e) {
                    echo "<p>Errore nella  query per il quesito $numeroQuesito: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
            } else {
                echo "<p>La query per il quesito #$numeroQuesito non è valida!</p>";
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concludiTest'])) {
    $pdo->beginTransaction();
    try {
        // Recupera tutte le risposte di codice
        $stmtRisposte = $pdo->prepare("CALL GetRisposteCodice(?, ?)");
        $stmtRisposte->execute([$userEmail, $testId]);
        $risposte = $stmtRisposte->fetchAll(PDO::FETCH_ASSOC);
        $stmtRisposte->closeCursor();
        if (empty($risposte)) {
            echo "Nessuna risposta di codice trovata.<br>";
        }
        foreach ($risposte as $risposta) {
            if (preg_match('/^\s*SELECT\s+.+?\s+FROM\s+([a-zA-Z0-9_]+)/i', $risposta['testoRisposta'], $matches)) {
                $nomeTabellaUtente = $matches[1];
                $nomeTabellaCompleto = "Tabella_di_Esercizio_" . $nomeTabellaUtente;
                $risposta['testoRisposta'] = str_ireplace("from " . $nomeTabellaUtente, "from " . $nomeTabellaCompleto, $risposta['testoRisposta']);
            }
            // Per ogni risposta
            $stmtSoluzioni = $pdo->prepare("CALL GetSoluzioni(?, ?)");
            $stmtSoluzioni->execute([$risposta['numeroQuesito'], $testId]);
            $soluzioni = $stmtSoluzioni->fetchAll(PDO::FETCH_COLUMN);

            // Controlla se la risposta dello studente si trova in soluzione, 1 è vero, 0 è falso
            $esito = in_array($risposta['testoRisposta'], $soluzioni) ? 1 : 0;
            $stmtSoluzioni->closeCursor();
            try {
                // Aggiorna l'esito della risposta
                echo "Esito prima dell'aggiornamento: $esito, ID Risposta: " . $risposta['idRisposta'] . "\n";
                $stmtAggiornaEsito1 = $pdo->prepare("CALL UpdateEsito(?,?)");
                echo $esito;
                $success =$stmtAggiornaEsito1->execute([$esito, $risposta['idRisposta']]);
                if ($success) {
                    echo "Aggiornamento esito riuscito della risposta: " . $risposta['idRisposta'] ;
                } else {
                    echo "Aggiornamento non riuscito: " . $risposta['idRisposta'] ;
                }
            } catch (PDOException $e) {
                error_log('Errore nell\'aggiornamento di esito: ' . $e->getMessage());
            }
        }
        // Verifica delle risposte ai quesiti chiusi
        $stmtRisposteChiuse = $pdo->prepare("CALL GetRispostaChiusa(?, ?)");
        $stmtRisposteChiuse->execute([$userEmail, $testId]);
        $risposteChiuse = $stmtRisposteChiuse->fetchAll(PDO::FETCH_ASSOC);
        $stmtRisposteChiuse->closeCursor();

        foreach ($risposteChiuse as $risposta) {
            $esito = ($risposta['idOpzione'] == $risposta['id']) ? 1 : 0;
            $stmtAggiornaEsito = $pdo->prepare("CALL UpdateEsito(?, ?)");
            $stmtAggiornaEsito->execute([$esito, $risposta['idRisposta']]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Si è verificato un errore: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concludiTest'])) {
    $pdo->beginTransaction();
    try {
        $pdo->commit();
        header("Location: risultatiTest.php?testId=" . urlencode($testId));
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $messaggiErrore[] = "Si è verificato un errore: " . $e->getMessage();
    }
}

$quesiti = $pdo->prepare("CALL GetQuesiti(?)");
$quesiti->execute([$testId]);
$quesiti = $quesiti->fetchAll(PDO::FETCH_ASSOC);

//logica logout
$query = "CALL GetTipoUtente(:email)";
$sto = $pdo->prepare($query);
$sto->execute(['email' => $userEmail]);
$user = $sto->fetch(PDO::FETCH_ASSOC);
$sto->closeCursor();
if ($user) {
    // Se l'utente è trovato, determina la dashboard di destinazione
    $dashboardDestinazione = $user['tipo'] === 'Studente' ? "dashboardStudente.php" : "dashboardDocente.php";
} else {
    // Gestisci il caso in cui l'utente non sia trovato
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Visualizza Quesiti</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body{
        background-color: cadetblue;
    }
</style>
<body>
<div style="margin-left: 20px;">
    <?php if ($fotoTest !== null): ?>
        <div>
            <img src="<?= $fotoTest ?>" style="max-width: 100px; height: auto;" alt="Foto del Test" >
        </div>
    <?php endif; ?>
    <h1>Quesiti del Test:</h1>
    <?php foreach ($messaggiErrore as $errore): ?>
        <div class="alert alert-warning" role="alert" style="margin-left: 2%; width: 94%;">
            <?= htmlspecialchars($errore) ?>
        </div>
    <?php endforeach; ?>
    <?php if ($statoTest !== 'Concluso'): ?>
        <?php foreach ($quesiti as $quesito): ?>
            <div>
                <h3><p><strong>Quesito:</strong><?= htmlspecialchars($quesito['NUMERO']) ?></h3>
                <?php if ($quesito['tipo'] === 'codice'): ?>
                    <p><?= htmlspecialchars($quesito['DESCRIZIONE']) ?></p>
                    <form method="POST">
                        <textarea name="rispostaCodice[<?= $quesito['NUMERO'] ?>]" rows="4" cols="50" style="resize: none;"></textarea>
                        <br>
                        <br>
                        <button type="submit" class="btn btn-primary">Invia Risposta</button>
                    </form>
                    <?php
                    if (isset($risultatiQuery[$quesito['NUMERO']])) {
                        echo "<h3>Risultato del quesito {$quesito['NUMERO']}</h3><table>";
                        echo "<tr>";
                        foreach ($risultatiEsercizio[0] as $colonna => $valore) {
                            echo "<th>" . htmlspecialchars($colonna) . "</th>";
                        }
                        echo "</tr>";
                        foreach ($risultatiEsercizio as $riga) {
                            echo "<tr>";
                            foreach ($riga as $colonna => $valore) {
                                echo "<td style='padding-right:20px;'>". htmlspecialchars($valore) . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "<br>";
                    }
                    ?>
                <?php elseif($quesito['tipo'] === 'chiuso'): ?>
                    <!--Quesiti chiusi-->
                    <?php
                    $opzioni = $pdo->prepare("SELECT id, testo FROM Opzione WHERE numero = :numero AND titolo = :titolo");
                    $opzioni->bindParam(':numero', $quesito['NUMERO'], PDO::PARAM_INT);
                    $opzioni->bindParam(':titolo', $testId, PDO::PARAM_STR);
                    $opzioni->execute();
                    $opzioni = $opzioni->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <form method="POST">
                        <input type="hidden" name="numeroQuesito" value="<?= $quesito['NUMERO'] ?>">
                        <p><?= htmlspecialchars($quesito['DESCRIZIONE']) ?></p>
                        <?php foreach ($opzioni as $opzione): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="risposta[<?= $quesito['NUMERO'] ?>][]" value="<?= $opzione['id'] ?>" id="opzione<?= $opzione['id'] ?>">
                                <label class="form-check-label" for="opzione<?= $opzione['id'] ?>">
                                    <?= htmlspecialchars($opzione['testo']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary">Invia Risposta</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <form method="POST">
            <a href="<?php echo $dashboardDestinazione; ?>"  class="btn btn-warning">Torna alla Dashboard</a>
            <button type="submit" name="concludiTest" class="btn btn-danger">Concludi Test</button>
        </form>
    <?php else: ?>
        <div class="alert alert-success" role="alert" style='padding:30px; margin:120px; text-align: center; background-color: darkred; color: white'>
            Il test è concluso, non è possibile visualizzare i quesiti.
        </div>
        <div class="text-center mt-4" style="border-top: 3px;">
            <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-primary">Torna alla Dashboard</a>
        </div>
    <?php endif; ?>
</body>
</html>
