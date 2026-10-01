<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifica Quesiti</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Classifica Quesiti</h2>

    <?php
    include 'conn.php'; 

    try {
        $stmt = $pdo->query("CALL VisualizzaClassificaQuesiti()");

        if ($stmt->rowCount() > 0) {
            //visualizzo i dati se esistenti e creo tabella
            echo "<table class='table table-bordered table-hover'>";
            echo "<thead class='thead-dark'>";
            echo "<tr><th>Numero</th><th>Titolo</th><th>Difficoltà</th><th>Numero Risposte</th><th>Descrizione</th></tr>";
            echo "</thead>";
            echo "<tbody>";

            // Ciclo per ogni riga di risultato
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
                echo "<td>" . htmlspecialchars($row['titolo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['difficolta']) . "</td>";
                echo "<td>" . htmlspecialchars($row['num_risposte']) . "</td>";
                echo "<td>" . htmlspecialchars($row['descrizione']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            // Visualizzare un messaggio se non ci sono dati disponibili
            echo "<div class='alert alert-warning' role='alert'>Non ci sono ancora test o quesiti per poter mostrare una classifica.</div>";
        }
    } catch (PDOException $e) {
        die("Errore: " . $e->getMessage());
    }
    ?>
</div>
<div class="container mt-3">
    <a href="statistiche.php" class="btn btn-secondary">Torna indietro</a>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
