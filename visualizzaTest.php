<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}

try {
    $query = "CALL GetTest()"; // Ottiene tutte le informazioni dai test
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //riceve il tipo di user
    $query = "CALL GetTipoUtente(:email)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $_SESSION['user_email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $dashboardDestinazione = $user['tipo'] === 'Studente' ? "dashboardStudente.php" : "dashboardDocente.php";



} catch (Exception $e) {
    die("Errore nella connessione al database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>

</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Visualizza i test disponibili</h1>

    <?php
    if (empty($tests)) {
        echo '<div class="alert alert-info" role="alert">Non ci sono test disponibili al momento.</div>';
        echo '<a href="' . $dashboardDestinazione . '" class="btn btn-secondary">Torna indietro</a>';
    } else {
        ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Data</th>
                    <th>Foto</th> 
                    <th>Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tests as $test): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($test['titolo']); ?></td>
                        <td><?php echo htmlspecialchars($test['data']); ?></td>
                        <td>
                            <?php if (!empty($test['foto'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($test['foto']); ?>" alt="Foto Test" style="width: 100px;"> <!-- Mostra la foto -->
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="visualizzaQuesitiTest.php" method="get">
                                <input type="hidden" name="testId" value="<?php echo htmlspecialchars($test['titolo']); ?>">
                                <input type="submit" class="btn btn-primary" value="Visualizza Quesiti">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
            <div class="row mt-2">
                <div class="col">
                    <a href="<?php echo $dashboardDestinazione; ?>" class="btn btn-secondary">Torna indietro</a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
</body>
</html>
