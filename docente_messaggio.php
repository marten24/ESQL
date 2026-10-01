<?php
require 'conn.php'; 
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: loginDocente.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo_messaggio = $_POST['titolo_messaggio'];
    $testo = $_POST['testo'];
    $titolo = $_POST['titolo'];
    $email_docente = $_SESSION['user_email'];
    $data = date('Y-m-d');

    $sql = "CALL InserisciMessaggio(?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titolo_messaggio, $testo, $data, $titolo, $email_docente]);

    if ($stmt) {
        header("Location: dashboardDocente.php?message=" . urlencode("Messaggio inviato con successo!"));
        exit();
    } else {
        header("Location: dashboardDocente.php?message=" . urlencode("Errore nell'invio del messaggio."));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invia Messaggio</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Invia un nuovo messaggio</h1>
    <form action="docente_messaggio.php" method="post">
        <div class="form-group">
            <label for="titolo_messaggio">Titolo del messaggio</label>
            <input type="text" class="form-control" id="titolo_messaggio" name="titolo_messaggio" required>
        </div>
        <div class="form-group">
            <label for="testo">Testo del messaggio</label>
            <textarea class="form-control" id="testo" name="testo" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="titolo">Titolo</label>
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
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Torna indietro</button>
    </form>
</div>
</body>
</html>
