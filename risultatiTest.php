<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}
$userEmail = $_SESSION['user_email'];
$testId = $_GET['testId']; // Assicurati che questo valore venga passato correttamente

$stmtVisualizzaRisposte = $pdo->prepare("SELECT visualizza_risposte FROM Test WHERE titolo = ?");
$stmtVisualizzaRisposte->execute([$testId]);
$visualizzaRisposte = $stmtVisualizzaRisposte->fetch(PDO::FETCH_ASSOC);

// Risultati per risposte chiuse
$stmtRisposteChiuse = $pdo->prepare("
     SELECT 
        q.descrizione, 
        r.esito, 
        o.testo AS risposta,
        'chiuso' AS tipoRisposta
    FROM 
        Risposta r
    INNER JOIN 
        RispostaChiusa rc ON r.idRisposta = rc.idRisposta
    INNER JOIN 
        Opzione o ON rc.idOpzione = o.id
    INNER JOIN 
        Quesito q ON r.numeroQuesito = q.numero AND r.titolo = q.titolo
    WHERE 
        r.userEmail = ? AND r.titolo = ?
");
$stmtRisposteChiuse->execute([$userEmail, $testId]);
$risposteChiuse = $stmtRisposteChiuse->fetchAll(PDO::FETCH_ASSOC);

// Risultati per risposte di codice
$stmtRisposteCodice = $pdo->prepare("
    SELECT 
        q.descrizione, 
        r.esito, 
        rc.testoRisposta AS risposta,
        'codice' AS tipoRisposta
    FROM 
        Risposta r
    INNER JOIN 
        RispostaCodice rc ON r.idRisposta = rc.idRisposta
    INNER JOIN 
        Quesito q ON r.numeroQuesito = q.numero AND r.titolo = q.titolo
    WHERE 
        r.userEmail = ? AND r.titolo = ?
");
$stmtRisposteCodice->execute([$userEmail, $testId]);
$risposteCodice = $stmtRisposteCodice->fetchAll(PDO::FETCH_ASSOC);

// Unisce i risultati delle due query
$risultati = array_merge($risposteChiuse, $risposteCodice);

// Calcola il numero di risposte corrette e il totale delle risposte
$numeroCorrette = array_sum(array_column($risultati, 'esito'));
// Sfondo verde se 60% sono corrette, altrimenti è rosso
$percentualeCorrette = count($risultati) > 0 ? ($numeroCorrette / count($risultati)) * 100 : 0;
$coloreSfondo = $percentualeCorrette >= 60 ? 'palegreen' : 'salmon';

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
    <title>Risultati Test</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            margin-bottom: 15px;
        }
        .corr { background-color: <?= $coloreSfondo; ?>;
            padding: 30px;
            margin:100px;
            margin-bottom: 10px;
            border: black solid 2px;
            border-radius: 5px;
        }
        .corr1 { background-color: darkred;
            padding: 30px;
            margin:100px;
            margin-bottom: 10px;
            border: black solid 2px;
            border-radius: 5px;
            color: white;
        }
    </style>
</head>
<body style="background-color: lightgrey">
<h1>Risultati del Test: <?= htmlspecialchars($testId) ?></h1>
<br>
<?php if ($visualizzaRisposte['visualizza_risposte'] == 0): ?>
    <p class="corr1">Attendi che le risposte vengano visualizzate.</p>
    <div class="text-center mt-4" style="bottom:20px; position: absolute; left: 45%;">
        <a href="dashboardStudente.php" class="btn btn-primary">Torna alla Dashboard</a>
    </div>
<?php else: ?>
    <?php foreach ($risultati as $risultato): ?>
        <div>
            <p><strong>Quesito:</strong> <?= htmlspecialchars($risultato['descrizione'] ?? "") ?></p>
            <?php if ($risultato['tipoRisposta'] === 'chiuso'): ?>
                <p><strong>Risposta fornita:</strong> <?= htmlspecialchars($risultato['risposta'] ?? "") ?></p>
            <?php elseif ($risultato['tipoRisposta'] === 'codice'): ?>
                <p><strong>Risposta fornita di codice:</strong> <?= nl2br(htmlspecialchars($risultato['risposta'] ?? "")) ?></p>
            <?php endif; ?>
            <p><strong>Esito:</strong> <?= $risultato['esito'] == '1' ? 'Corretta' : 'Errata' ?></p>
            <br>
        </div>
    <?php endforeach; ?>
    <div>
        <p class="corr"<strong>Risposte corrette:</strong> <?= $numeroCorrette ?> su <?= count($risultati) ?></p>
    </div>
    <div class="text-center mt-4" style="border-top: 3px;">
        <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-primary">Torna alla Dashboard</a>
    </div>
<?php endif; ?>
</body>
</html>
