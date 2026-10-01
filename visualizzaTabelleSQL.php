<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabelle SQL</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: 	#e0ffff;
        }
        .custom {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Le tue tabelle di esercizio</h2>
    <form action="modificaTabelleSQL.php" method="post" id="selectionForm">
        <?php
        require 'conn.php';
        session_start();

        // Variabili
        if (!isset($_SESSION['user_email'])) {
            header("Location: loginDocente.php");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $_SESSION['nomeTabella'] = $_POST['nomeTabella'];
        }

        // Chiamata alla SP VisualizzaTabelleSQL
        $user_email = $_SESSION['user_email'];
        $stmt = $pdo->prepare("CALL VisualizzaTabelleSQL(:user_email)");
        $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $tableName = $row['NOME'];
            $query = "SELECT * FROM Tabella_di_esercizio_$tableName";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h3>$tableName:</h3>";
            echo '<input type="radio" name="nomeTabella" value="' . $tableName . '"> Modifica ' . $tableName;
            if (!empty($tableData)) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-hover">';
                echo '<thead class="custom">';

                echo '<tr>';
                foreach ($tableData[0] as $columnName => $value) {
                    echo '<th scope="col">' . $columnName . '</th>';
                }
                echo '</tr>';

                echo '</thead>';
                echo '<tbody>';

                foreach ($tableData as $row) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td>' . $value . '</td>';
                    }
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p>La tabella è vuota </p>';
            }
        }
        ?>

        <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary mr-1" >Modifica</button>
            <a href="tabelleSQL.php" class="btn btn-danger">Indietro</a>
        </div>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
