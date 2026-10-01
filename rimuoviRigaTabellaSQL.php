<?php
session_start();
require 'conn.php';

$error = '';

// Variabili
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeTabella'])) {
    $_SESSION['nomeTabella'] = $_POST['nomeTabella'];
}

$nome_tabella = $_SESSION['nomeTabella'];
$nomeTabellaCompleto= "tabella_di_esercizio_$nome_tabella";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['righe_selezionate'])) {
        // Ottiengo i valori delle checkbox selezionate
        $righe_selezionate = $_POST['righe_selezionate'];

        // Per ogni ID
        foreach ($righe_selezionate as $id) {
            try {
                $query = "DELETE FROM $nomeTabellaCompleto WHERE id = '$id'";
                $stmt = $pdo->prepare($query);
                if (!$stmt->execute()) {
                    $error = ("Errore durante l'esecuzione della query: " . $stmt->errorInfo()[2]);
                }
            } catch (PDOException $e) {
                $error = "Errore durante l'eliminazione della riga: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rimuovi Righe</title>
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
<div class="container mt-3">
    <h1 class="text-center mb-4">Tabella <u><?php echo $nome_tabella; ?></u></h1>
    <h5 class="text-center mb-4">Seleziona righe da eliminare</h5>
    <?php if ($error != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <?php
        if (!isset($_SESSION['user_email'])) {
            header("Location: loginDocente.php");
            exit();
        }
        $user_email = $_SESSION['user_email'];
        $stmt = $pdo->prepare("CALL VisualizzaTabelleSQL(:user_email)");
        $stmt->bindParam(':user_email', $user_email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            foreach ($result as $row) {
                if ($row['NOME'] == $nome_tabella) {
                    $query = "SELECT * FROM Tabella_di_esercizio_$nome_tabella";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            echo '<td><input type="checkbox" name="righe_selezionate[]" value="' . $row['id'] . '"></td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo '<div class="d-flex justify-content-center">';
                        echo '<h4>La tabella è vuota</h4>';
                        echo '</div>';
                    }
                    break;
                }
            }
        }
        ?>
        <div class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-danger mr-1">Rimuovi</button>
            <a href="visualizzaTabelleSQL.php" class="btn btn-primary">Indietro</a>
        </div>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
