<?php
require 'conn.php';
session_start(); // Avvia la sessione
if (!isset($_SESSION['user_email'])) {
    // Se l'utente non è loggato, reindirizzalo alla pagina di login o registrazione
    header("Location: loginStudente.php");
    exit();
}
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';

try {
    $query = $pdo->prepare("CALL GetTestTitolo()");
    $query->execute();;
    $tests = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Errore nella connessione al database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="it">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Studente</title>
    <style>
        body{
            background-color: 	#e0ffff;
        }
        .btn-c {
            width: 100%; 
            display: block; 
        }
    </style>
</head>
<body>
<div class="container mt-5">
<h1><i class="fas fa-user-graduate"></i> <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo strpos($message, "Errore") !== false ? 'danger' : 'success'; ?>" role="alert">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<h5>Benvenuto nella dashboard studente!</h5>
<div>
    <br>
    <h2>Test:</h2>
    <form action="visualizzaQuesiti.php" method="get">
        <select name="testId">
            <?php foreach ($tests as $test): ?>
                <option value="<?= htmlspecialchars($test['TITOLO']) ?>"><?= htmlspecialchars($test['TITOLO']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" class="btn btn-warning" value="Visualizza Quesiti" style="margin-left: 20px;">
    </form>
    <div class="col-md-6" style="margin-top: 60px; padding: 0;width: 220px;">
        <h2>Messaggi:</h2>
        <a href="studente_messaggio.php" class="btn btn-primary">Invia messaggio ad un docente</a>
    </div>
    <div class="col-md-6" style="margin-top: 10px; padding: 0; width: 220px;">
        <a href="visualizza_messaggi_studente.php" class="btn btn-primary">Visualizza messaggi ricevuti</a>
    </div>
    <div class="col-md-6" style="margin-top: 20px; padding: 0;width: 220px;">
        <h2>Altro:</h2>
        <div class="row mt-2" >
            <div class="col" style=" width: 220px;">
                <a href="statistiche.php" class="btn btn-primary btn-c">Statistiche</a>
                <div class="row mt-2">
                    <div class="col">
                        <a href="visualizzaTest.php" class="btn btn-primary btn-c">Visualizza Test Disponibili</a>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <br>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
</html>
