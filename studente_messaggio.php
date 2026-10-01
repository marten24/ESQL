<?php
require 'conn.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo_messaggio = $_POST['titolo_messaggio'];
    $testo = $_POST['testo'];
    $titolo = $_POST['titolo'];
    $email_studente = $_SESSION['user_email']; 
    $data = date('Y-m-d');

    // Ottiene l'email del docente dal titolo selezionato
    $sqlDocente = "CALL GetEmailDocenteByTitolo(?)";
    $stmtDocente = $pdo->prepare($sqlDocente);
    $stmtDocente->execute([$titolo]);
    $email_docente = ($stmtDocente->fetch(PDO::FETCH_ASSOC))['email'];
    $stmtDocente->closeCursor();
    $sql = "CALL InserisciMessaggioStudente(?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titolo_messaggio, $testo, $data, $titolo, $email_studente, $email_docente]);

    if ($stmt) {
        $message = urlencode("Messaggio inviato con successo al docente con indirizzo email: $email_docente!");
        header("Location: dashboardStudente.php?message=$message");
        exit();
    } else {
        header("Location: dashboardStudente.php?message=" . urlencode("Errore nell'invio del messaggio."));
        exit();
    }
}

$user_email = $_SESSION['user_email']; 

$query = "CALL GetTipoUtente(:email)";

$stmt = $pdo->prepare($query);

$stmt->execute(['email' => $user_email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Se l'utente è trovato, determina la dashboard di destinazione
    $dashboardDestinazione = $user['tipo'] === 'Studente' ? "dashboardStudente.php" : "dashboardDocente.php";
} else {
    // Gestisce il caso in cui l'utente non sia trovato
    header("Location: index.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invia Messaggio - Studente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Invia un nuovo messaggio come studente</h1>
    <form action="studente_messaggio.php" method="post">
        <div class="form-group">
            <label for="titolo_messaggio">Titolo del messaggio</label>
            <input type="text" class="form-control" id="titolo_messaggio" name="titolo_messaggio" required>
        </div>
        <div class="form-group">
            <label for="testo">Testo del messaggio</label>
            <textarea class="form-control" id="testo" name="testo" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="titolo">Titolo del test</label>
            <select class="form-control" id="titolo" name="titolo" required>
                <?php
                $sql = "CALL GetTitoliTest()";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $titoli = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($titoli as $row): ?>
                    <option><?php echo htmlspecialchars($row['TITOLO']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Invia</button>
        <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-secondary">Torna indietro</a>

    </form>
</div>
</body>
</html>
