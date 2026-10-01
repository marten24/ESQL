<?php
require 'conn.php';
session_start(); // Avvia la sessione
$message = '';
if (!isset($_SESSION['user_email'])) {
    // Se l'utente non è loggato, reindirizzalo alla pagina di login o registrazione
    header("Location: loginDocente.php");
    exit();
}
try {
    // Se il modulo è stato inviato
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica se tutti i campi sono stati compilati
        if (isset($_POST['titolo']) && isset($_POST['data'])) {
            $titolo = $_POST['titolo'];
            $data = $_POST['data'];
            $visualizza_risposte = 0; // 0 è false

            // Chiama la stored procedure per inserire il test nella tabella Test
            $inserisciTestSP = "CALL InserisciTest(:titolo, :data, :visualizza_risposte, :email)";
            $statement = $pdo->prepare($inserisciTestSP);
            $statement->bindParam(':titolo', $titolo);
            $statement->bindParam(':data', $data);
            $statement->bindParam(':visualizza_risposte', $visualizza_risposte);
            $statement->bindParam(':email', $_SESSION['user_email']);
            $statement->execute();

            // Caricamento e salvataggio dell'immagine
            $foto_nome = $_FILES['foto']['name'];
            $foto_temp = $_FILES['foto']['tmp_name'];
            $foto_dimensioni = $_FILES['foto']['size'];

            // Verifica se è stata caricata un'immagine
            if ($foto_dimensioni > 0) {
                $foto_contenuto = file_get_contents($foto_temp);

                // Chiama la stored procedure per inserire la foto nella tabella Test_foto
                $inserisciFotoSP = "CALL InserisciFotoTest(:titolo, :foto)";
                $statement = $pdo->prepare($inserisciFotoSP);
                $statement->bindParam(':titolo', $titolo);
                $statement->bindParam(':foto', $foto_contenuto, PDO::PARAM_LOB);
                $statement->execute();
            }

            $message = "Test inserito correttamente.";

        } else {
            $message = "Compila tutti i campi.";
        }
    }
} catch (PDOException $e) {

    error_log("Errore durante l'inserimento del test nel database: " . $e->getMessage());

    if ($e->getCode() == '08S01') {
        // Codice specifico per l'errore di dimensione del pacchetto
        $message = "La foto è troppo grande. Prova a ridurre le dimensioni o cambiare formato della foto e riprova.";
    }elseif ($e->getCode() == '23000'){
        // Codice specifico per l'errore di chiave primaria
        $message = "Il titolo del test esiste già. Cambialo e riprova.";
    }
    else {
        // Gestione degli altri tipi di errore
        $message = "Errore durante l'inserimento del test nel database: " . $e->getMessage();
}

}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creazione Test</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Creazione Test</h1>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message === "Test inserito correttamente." ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titolo">Titolo</label>
            <input type="text" class="form-control" id="titolo" name="titolo" required>
        </div>
        <div class="form-group">
            <label for="data">Data</label>
            <input type="date" class="form-control" id="data" name="data" required>
        </div>
        <div class="form-group">
            <label for="foto">Foto</label>
            <input type="file" class="form-control-file" id="foto" name="foto">
        </div>
        <button type="submit" class="btn btn-primary">Crea Test</button>
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Torna indietro</button>
    </form>
</div>
</body>
</html>
