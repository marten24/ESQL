<?php
require 'conn.php'; 
session_start();

if (!isset($_SESSION['user_email'])) {
    // Se l'utente non è loggato, reindirizzalo alla pagina di login o registrazione
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['user_email']; 

$query = "CALL GetTipoUtente(:email)";

$stmt = $pdo->prepare($query);

$stmt->execute(['email' => $user_email]);

// Ottieni il risultato
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Se l'utente è trovato, determina la dashboard di destinazione
    $dashboardDestinazione = $user['tipo'] === 'Studente' ? "dashboardStudente.php" : "dashboardDocente.php";
} else {
    //caso in cui l'utente non sia trovato
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color:  #e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1> Seleziona statistica</h1>
    <div class="row">
        <div class="col">
            <a href="statistiche_quesiti.php" class="btn btn-primary">Statistiche Quesiti (ord. per risposta)</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="classifica_studenti_risposte.php" class="btn btn-primary">Classifica studenti (risposte corrette)</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="VisualizzaClassificaFiniti.php" class="btn btn-primary">Classifica Numero Test Completati </a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-secondary">Torna indietro</a>
        </div>
    </div>
</div>
</body>
</html>
