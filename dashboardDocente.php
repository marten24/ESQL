<?php
require 'conn.php';
session_start(); // Avvia la sessione
if (!isset($_SESSION['user_email'])) {
    // Se l'utente non è loggato, reindirizzalo alla pagina di login o registrazione
    header("Location: loginDocente.php");
    exit();
}

// Controlla se c'è un messaggio nella string
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard docente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
     <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo strpos($message, "Errore") !== false ? 'danger' : 'success'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <h5>Benvenuto nella dashboard docente!</h5>
    <br>
    <div class="row">
        <div class="col">
            <a href="tabelleSQL.php" class="btn btn-primary">Gestisci tabelle SQL</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="creaTest.php" class="btn btn-primary">Crea Test</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="creaQuesito.php" class="btn btn-primary">Crea un quesito</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="docente_messaggio.php" class="btn btn-primary">Invia messaggio</a>
        </div>
</div>
    <div class="row mt-2">
        <div class="col">
            <a href="docente_visualizza_messaggio.php" class="btn btn-primary">Visualizza messaggi</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="gestisci_visibilità.php" class="btn btn-primary">Gestione Visibilità risposte</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="statistiche.php" class="btn btn-primary">Statistiche</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="visualizzaTest.php" class="btn btn-primary">Visualizza Test Disponibili</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
</div>
</div>
</body>
</html>
