<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['numero']) || !isset($_SESSION['titolo'])) {
    // Se le variabili di sessione non sono state inizializzate, reindirizza alla pagina precedente
    header("Location: creaQuesito.php");
    exit();
}

$numero = $_SESSION['numero'];
$titolo = $_SESSION['titolo'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Inserisci le soluzioni nel database
        foreach ($_POST['soluzione'] as $soluzione) {
            $stmt = $pdo->prepare("CALL InserisciSoluzione(:numero, :titolo, :testo)");
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':titolo', $titolo);
            $stmt->bindParam(':testo', $soluzione);
            $stmt->execute();
        }

        // Elimina le variabili di sessione non più necessarie
        unset($_SESSION['numero']);
        unset($_SESSION['titolo']);

        $message = "Soluzioni di codice inserite correttamente!";
        header("Location: dashboardDocente.php?message=" . urlencode($message));
        exit();
    } catch (PDOException $e) {
        error_log("Errore durante l'inserimento delle soluzioni di codice nel database: " . $e->getMessage());
        $message = "Errore durante l'inserimento delle soluzioni di codice nel database: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Soluzioni di Codice</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Inserisci Soluzioni di Codice</h1>
    <?php if (isset($message)): ?>
        <div class="alert <?php echo isset($message) && $message === "Soluzioni di codice inserite correttamente!" ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="inserisci_codice.php" method="POST">
        <div id="soluzioni_container">
            <div class="form-group">
                <label for="soluzione_1">Soluzione di Codice 1:</label>
                <input type="text" class="form-control" id="soluzione_1" name="soluzione[]" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Inserisci Soluzioni di Codice</button>
        <button type="button" class="btn btn-success" id="aggiungi_soluzione"><i class="fas fa-plus"></i> Aggiungi Soluzione di Codice</button>
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Annulla</button>
    </form>
</div>
<script>
    document.getElementById('aggiungi_soluzione').addEventListener('click', function() {
        // Incrementa il numero di soluzioni
        var num_soluzioni = document.querySelectorAll('[name="soluzione[]"]').length + 1;

        // Crea un nuovo campo per la soluzione di codice
        var nuova_soluzione = '<div class="form-group"><label for="soluzione_' + num_soluzioni + '">Soluzione di Codice ' + num_soluzioni + ':</label><input type="text" class="form-control" id="soluzione_' + num_soluzioni + '" name="soluzione[]" required></div>';

        // Aggiungi il nuovo campo prima dei pulsanti
        document.getElementById('soluzioni_container').insertAdjacentHTML('beforeend', nuova_soluzione);
    });
</script>

</body>
</html>
