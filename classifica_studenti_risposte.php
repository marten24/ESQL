<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifica Studenti</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color:  #e0ffff;
        }
        .custom {
            background-color: #007bff;
            color: #fff;
        }
        .table-hover {
            background-color: #EECDF8;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Classifica Studenti in base alle risposte corrette</h2>
    <?php
    include 'conn.php';

    try {
        // Chiamata alla SP VisualizzaClassificaStudenti
        $stmt = $pdo->query("CALL VisualizzaClassificaStudenti()");
        echo "<table class='table table-bordered table-hover'>";
        echo "<thead class='custom'>";
        echo "<tr><th>Codice Studente</th><th>Totale Risposte</th><th>Risposte Corrette</th><th>Percentuale Risposte Corrette</th></tr>";
        echo "</thead>";
        echo "<tbody>";

        // Ciclo per ogni riga del risultato
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['CodiceStudente']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TotaleRisposte']) . "</td>";
            echo "<td>" . htmlspecialchars($row['RisposteCorrette']) . "</td>";
            // Colora la cella della percentuale
            $percentuale = $row['PercentualeCorrette'] * 100;
            $color = '';
            if ($percentuale >= 70) {
                $color = 'green';  // Verde per percentuali alte
            } elseif ($percentuale >= 50) {
                $color = 'yellow'; // Giallo per percentuali medie
            } else {
                $color = 'red'; // Rosso per percentuali basse
            }
            echo "<td style='color: $color;'>" . number_format($percentuale, 0) . "%</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
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
