<?php
require 'conn.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera i dati dal form
    $test = $_POST['test'];
    $difficolta = $_POST['difficolta'];
    $num_risposte = 0;
    $descrizione = $_POST['descrizione'];
    $tipo = $_POST['tipo'];

try {
    // Chiama la stored procedure per inserire il quesito nel database
    $stmt = $pdo->prepare("CALL InserisciQuesito(:test, :difficolta, :num_risposte, :descrizione, @numero)");
    $stmt->bindParam(':test', $test);
    $stmt->bindParam(':difficolta', $difficolta);
    $stmt->bindParam(':num_risposte', $num_risposte);
    $stmt->bindParam(':descrizione', $descrizione);
    $stmt->execute();

    // Recupera il numero del quesito inserito 
    $stmt = $pdo->query("SELECT @numero AS numero");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $numero = $result['numero'];
    //salva variabili di sessione
    $_SESSION['numero'] = $numero;
    $_SESSION['titolo'] = $test;
    //var titolo contiene il param di test
    $titolo = $test;
    // Reindirizza in base al tipo di quesito
    if ($tipo == 'chiuso') {
        // Inserisci il titolo e il numero nella tabella Quesito_Chiuso
        $stmt2 = $pdo->prepare("CALL InserisciQuesitoChiuso(:numero, :titolo)");
        $stmt2->bindParam(':numero', $numero);
        $stmt2->bindParam(':titolo', $titolo);
        $stmt2->execute();
        $stmt2->closeCursor(); 

        header("Location: inserisci_opzioni.php");
        exit();
    } elseif ($tipo == 'codice') {
        // Inserisci il titolo e il numero nella tabella Quesito_Codice
        $stmt3 = $pdo->prepare("CALL InserisciQuesitoCodice(:numero, :titolo)");
        $stmt3->bindParam(':numero', $numero);
        $stmt3->bindParam(':titolo', $titolo);
        $stmt3->execute();
        $stmt3->closeCursor(); 
        header("Location: inserisci_codice.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Errore durante l'inserimento del quesito nel database: " . $e->getMessage());
    die("Errore durante l'inserimento del quesito nel database: " . $e->getMessage());
}
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Quesito</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Crea Quesito</h1>
    <form action="creaQuesito.php" method="POST">
        <div class="form-group">
            <label for="test">Seleziona il Titolo del Test:</label>
            <select class="form-control" id="test" name="test">
                <?php
                // Recupera i titoli dei test dal database
                $query = "CALL GetTitoliTest()";
                $result = $pdo->query($query);
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option>' . $row['TITOLO'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="difficolta">Difficoltà:</label>
            <select class="form-control" id="difficolta" name="difficolta">
                <option value="Basso">Basso</option>
                <option value="Medio">Medio</option>
                <option value="Alto">Alto</option>
            </select>
        </div>
        <div class="form-group">
            <label for="descrizione">Descrizione:</label>
            <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="tipo">Tipo di Quesito:</label>
            <select class="form-control" id="tipo" name="tipo">
                <option value="chiuso">Quesito Chiuso</option>
                <option value="codice">Quesito di Codice</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Crea Quesito</button>
        <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Torna indietro</button>
    </form>
</div>
</body>
</html>
