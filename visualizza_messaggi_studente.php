<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: loginStudente.php");
    exit();
}
$user_email = $_SESSION['user_email']; 

try {
    $stmt = $pdo->prepare("CALL GetMessaggiStudente(:email_studente)");
    $stmt->bindParam(':email_studente', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Errore: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaggi Ricevuti</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1>Messaggi Ricevuti</h1>
    <div class="mt-3">
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Titolo del test</th>
                <th>Oggetto Messaggio</th>
                <th>Azione</th> 
                <th>Data</th>
                <th>Email Docente</th>
            </tr>
            </thead>
            <tbody>
            <?php if($result): ?>
                <?php foreach($result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']); ?></td>
                        <td><?= htmlspecialchars($row['titolo']); ?></td>
                        <td><?= htmlspecialchars($row['titolo_messaggio']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal<?= $row['id'] ?>">
                                Leggi Messaggio
                            </button>
                            <div class="modal fade" id="modal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>"><?= htmlspecialchars($row['titolo_messaggio']) ?></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <?= nl2br(htmlspecialchars($row['testo'])) ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($row['data']); ?></td>
                        <td><?= htmlspecialchars($row['email_docente']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Nessun messaggio ricevuto.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-secondary" onclick="location.href='dashboardStudente.php'">Torna indietro</button>
</div>

</body>
</html>
