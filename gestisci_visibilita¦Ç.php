<?php
require 'conn.php'; // Assicurati che questo file esista e stabilisca una connessione PDO con il tuo database.
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginDocente.php");
    exit();
}

try {
    $pdo->beginTransaction(); // Inizia transazione

    $sql = "CALL GetTitoliVisualizzaRisposteDocente(:emailDocente)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['emailDocente' => $_SESSION['user_email']]);

    // Leggi tutti i risultati e memorizzali in un array
    $results = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $row;
    }
    $stmt->closeCursor(); // Chiudi il cursore

    if (isset($_POST['toggleVisibility'])) {
        $titolo = $_POST['titolo'];
        $currentStatusQuery = "CALL GetVisualizzaRisposte(:titolo)";
        $currentStatusStmt = $pdo->prepare($currentStatusQuery);
        $currentStatusStmt->execute(['titolo' => $titolo]);
        $currentState = $currentStatusStmt->fetchColumn();
        $currentStatusStmt = null; // serve a liberare le risorse dello statement

        $nuovoStato = $currentState ? 0 : 1;

        $updateSql = "CALL AggiornaVisualizzaRisposte(:titolo, :nuovoStato)";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute(['nuovoStato' => $nuovoStato, 'titolo' => $titolo]);
        $updateStmt = null; // Libera le risorse dello statement

        $pdo->commit(); // Completa la transazione 

        header("Location: dashboardDocente.php?message=" . urlencode("Visibilità aggiornata con successo!"));
        exit();
    }
} catch (PDOException $e) {
    $pdo->rollBack(); // Annulla la transazione in caso di errore
    error_log("Errore durante la gestione della visibilità delle risposte: " . $e->getMessage());
    echo "Errore durante la gestione della visibilità delle risposte: " . $e->getMessage();
}


?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Visibilità Risposte</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Gestione Visibilità Risposte</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Titolo</th>
            <th>Visualizza Risposte</th>
            <th>Azione</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($results)): ?>
            <tr><td colspan="3">Non ci sono test disponibili.</td></tr>
        <?php else: ?>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['titolo']); ?></td>
                    <td><?php echo $row['visualizza_risposte'] ? 'Sì' : 'No'; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="titolo" value="<?php echo htmlspecialchars($row['titolo']); ?>">
                            <button type="submit" name="toggleVisibility" class="btn btn-sm <?php echo $row['visualizza_risposte'] ? 'btn-danger' : 'btn-success'; ?>">
                                <?php echo $row['visualizza_risposte'] ? 'Nascondi Risposte' : 'Mostra Risposte'; ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboardDocente.php" class="btn btn-primary">Torna alla Dashboard</a>
</div>
</body>
</html>
