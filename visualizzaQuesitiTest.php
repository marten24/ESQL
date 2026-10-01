<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}

$testId = isset($_GET['testId']) ? $_GET['testId'] : ''; // Riceve il test dalla richiesta GET

try {
    $query = "CALL GetQuesitoByTitolo(:testId)"; // Ottieni i quesiti per il test specificato
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':testId', $testId, PDO::PARAM_STR);
    $stmt->execute();
    $quesiti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //get tipo utente
    $query = "CALL GetTipoUtente(:email)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $_SESSION['user_email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $dashboardDestinazione = $user['tipo'] === 'Studente' ? "dashboardStudente.php" : "dashboardDocente.php";



} catch (Exception $e) {
    die("Errore nella connessione al database: " . $e->getMessage());
}

// Controllo se ci sono quesiti per il test
$noQuesiti = empty($quesiti);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Quesiti del Test</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body style="background-color: lightgrey">
<h1>Quesiti del Test: <?php echo htmlspecialchars($testId); ?></h1>

<?php if ($noQuesiti): ?>
   <div class="alert alert-info" role="alert">Non ci sono quesiti disponibili al momento.</div>

<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Descrizione</th>
                <th>Difficoltà</th>
                <th>Num Risposte</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($quesiti as $quesito): ?>
                <tr>
                    <td><?php echo htmlspecialchars($quesito['descrizione']); ?></td>
                    <td><?php echo htmlspecialchars($quesito['difficolta']); ?></td>
                    <td><?php echo htmlspecialchars($quesito['num_risposte']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="row mt-2">
    <div class="col">
        <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-secondary">Torna alla dashboard</a>
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <a href="visualizzaTest.php" class="btn btn-secondary">Torna  indietro</a>
    </div>
</div>



</body>
</html>
