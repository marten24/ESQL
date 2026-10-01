<?php
require 'conn.php';
session_start();

$errore = '';

// Variabili
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $data = $_POST['data'];
    $num_righe = $_POST['num_righe'];
    $user_email = $_SESSION['user_email'];

    try {
        // Creazione della nuova tabella con parametri
        $nomeTabella = "Tabella_di_Esercizio_$nome";
        $stmt = $pdo->prepare("CALL CreateTabellaSQLEsercizio(:nomeTabella)");
        $stmt->bindParam(":nomeTabella", $nomeTabella);
        $stmt->execute();

        // Creazione del trigger insert num_righe
        $triggerName = "aggiorna_insert_num_righe_$nomeTabella";
        $triggerQuery = "CREATE TRIGGER $triggerName AFTER INSERT ON $nomeTabella
    FOR EACH ROW
    BEGIN
        DECLARE nuovo_num_righe INT;
        SET nuovo_num_righe = (SELECT COUNT(*) FROM $nomeTabella);
        UPDATE tabella_sql SET num_righe = nuovo_num_righe WHERE nome = :nome;
    END;";

        $stmt = $pdo->prepare($triggerQuery);
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();

        // Creazione del trigger delete num_righe
        $triggerName = "aggiorna_delete_num_righe_$nomeTabella";
        $triggerQuery = "CREATE TRIGGER $triggerName AFTER DELETE ON $nomeTabella
    FOR EACH ROW
    BEGIN
        DECLARE nuovo_num_righe INT;
        SET nuovo_num_righe = (SELECT COUNT(*) FROM $nomeTabella);
        UPDATE tabella_sql SET num_righe = nuovo_num_righe WHERE nome = :nome;
    END;";

        $stmt = $pdo->prepare($triggerQuery);
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();

        // Chiamata alla SP InserisciTabellaSQL
        $stmt = $pdo->prepare("CALL InserisciTabellaSQL(:nome, :data, :num_righe, :user_email)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':num_righe', $num_righe);
        $stmt->bindParam(':user_email', $user_email);
        $stmt->execute();

        $successo = 'La tabella è stata creata correttamente!';

        // Salvo le variabili
        $_SESSION['nomeTabella'] = $nome;
        $_SESSION['numRighe'] = $num_righe;

        header("Location: inserisciAttributiTabelle.php");
        exit();
    } catch (PDOException $e) {
        $errore = "Errore durante la creazione della tabella " . $e->getMessage();
    }
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Tabella</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="text-center">Crea una tabella SQL!</h1>
            <?php if ($errore != ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errore; ?>
                </div>
            <?php endif; ?>
            <br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="form-group text-cen">
                    <label for="nome">Nome della tabella:</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Inserisci il nome della tabella" required>
                </div>
                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" class="form-control" id="data" name="data" required>
                </div>
                <div class="form-group">
                    <label for="num_righe">Numero di righe:</label>
                    <input type="number" class="form-control" id="num_righe" name="num_righe" placeholder="Inserisci il numero di righe" required>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary mr-1" style="width: 150px;">Crea Tabella</button>
                    <a href="tabelleSQL.php" class="btn btn-danger ml-1" style="width: 150px;">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Imposta l'attributo date con la data di oggi
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("data").value = today;
</script>
</body>
</html>
