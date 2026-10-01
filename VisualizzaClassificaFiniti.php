<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Classifica Studenti per Test Completati</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Classifica Studenti per Test Completati</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Codice Studente</th>
            <th>Numero Test Completati</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require 'conn.php'; 
        try {
            $stmt = $pdo->query("SELECT * FROM ClassificaStudentiTestCompletati");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['CodiceStudente']) . "</td>";
                echo "<td>" . htmlspecialchars($row['NumeroTestCompletati']) . "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            die("Errore: " . $e->getMessage());
        }
        ?>
        </tbody>
    </table>
</div>
<div class="text-center mt-4" style="padding: 50px;">
    <a href="dashboardStudente.php" class="btn btn-primary">Torna alla Dashboard</a>

</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
