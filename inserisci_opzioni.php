<?php
require 'conn.php';
session_start();

$message = '';

if (!isset($_SESSION['numero']) || !isset($_SESSION['titolo'])) {
    // Se le variabili di sessione non sono state inizializzate, reindirizza alla pagina precedente
    header("Location: creaQuesito.php");
    exit();
}

$numero = $_SESSION['numero'];
$titolo = $_SESSION['titolo'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $num_opzioni = count($_POST['opzione']);
        $opzioni_selezionate = isset($_POST['opzione_selezionata']) ? $_POST['opzione_selezionata'] : [];

        // Verifica se è stata selezionata almeno un'opzione corretta
        if (empty($opzioni_selezionate)) {
            // Nessuna opzione corretta selezionata, imposta il messaggio di errore
            $message = "Errore: Seleziona almeno un'opzione corretta!";
        } else {
            // Almeno un'opzione corretta è stata selezionata, procedi con l'inserimento
            for ($i = 0; $i < $num_opzioni; $i++) {
                $testo_opzione = $_POST['opzione'][$i];
                $stmt = $pdo->prepare("CALL InserisciOpzione(:numero, :titolo, :testo)");
                $stmt->bindParam(':numero', $numero);
                $stmt->bindParam(':titolo', $titolo);
                $stmt->bindParam(':testo', $testo_opzione);
                $stmt->execute();

                $stmtMaxIdOpzione = $pdo->prepare("CALL GetMaxIdOpzione()");
                $stmtMaxIdOpzione->execute();
                $row = $stmtMaxIdOpzione->fetch(PDO::FETCH_ASSOC);
                $last_option_id = $row['id'];
                $stmtMaxIdOpzione->closeCursor();

                // Verifica se l'opzione corrente è stata selezionata come corretta
                if (in_array($i + 1, $opzioni_selezionate)) {
                    $stmt = $pdo->prepare("CALL InserisciQuesitoChiusoOpzione(:id, :numero, :titolo)");
                    $stmt->bindParam(':id', $last_option_id);
                    $stmt->bindParam(':numero', $numero);
                    $stmt->bindParam(':titolo', $titolo);
                    $stmt->execute();
                }
            }

            $message = "Opzioni inserite correttamente!";
            header("Location: dashboardDocente.php?message=" . urlencode($message));
            exit();
        }

    } catch (PDOException $e) {
        error_log("Errore durante l'inserimento delle opzioni nel database: " . $e->getMessage());
        $message = "Errore durante l'inserimento delle opzioni nel database: " . $e->getMessage();
    }
}

?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Opzioni</title>
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
    <h1>Inserisci Opzioni</h1>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo $message === "Opzioni inserite correttamente!" ? 'alert-success' : 'alert-danger'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST">
        <div id="opzioni_container">
            <div class="form-group">
                <label for="opzione_1">Opzione 1:</label>
                <input type="text" class="form-control" id="opzione_1" name="opzione[]" required>
                <input type="checkbox" id="opzione_selezionata_1" name="opzione_selezionata[]" value="1">
                <label for="opzione_selezionata_1">Seleziona come corretta</label>
            </div>
            <div class="form-group">
                <label for="opzione_2">Opzione 2:</label>
                <input type="text" class="form-control" id="opzione_2" name="opzione[]" required>
                <input type="checkbox" id="opzione_selezionata_2" name="opzione_selezionata[]" value="2">
                <label for="opzione_selezionata_2">Seleziona come corretta</label>
            </div>

        </div>
        <button type="submit" class="btn btn-primary">Inserisci Opzioni</button>
        <button type="button" class="btn btn-success" id="aggiungi_opzione"><i class="fas fa-plus"></i> Aggiungi Opzione</button>
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Annulla</button>

    </form>
</div>

<script>
    document.getElementById('aggiungi_opzione').addEventListener('click', function() {
        var num_opzioni = document.querySelectorAll('[name="opzione[]"]').length + 1;
        var nuova_opzione = '<div class="form-group"><label for="opzione_' + num_opzioni + '">Opzione ' + num_opzioni + ':</label><input type="text" class="form-control" id="opzione_' + num_opzioni + '" name="opzione[]" required><input type="checkbox" id="opzione_selezionata_' + num_opzioni + '" name="opzione_selezionata[]" value="' + num_opzioni + '"><label for="opzione_selezionata_' + num_opzioni + '">Seleziona come corretta</label></div>';
        document.getElementById('opzioni_container').insertAdjacentHTML('beforeend', nuova_opzione);
    });

</script>

</body>
</html>
