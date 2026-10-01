<?php
require 'conn.php';
session_start(); // Avvia la sessione

if (!isset($_SESSION['user_email'])) {
    // Se l'utente non è loggato, reindirizzalo alla pagina di login
    header("Location: loginDocente.php");
    exit();
}
$user_email = $_SESSION['user_email'];
try {
    $stmt = $pdo->prepare("CALL GetMessaggiDocente(:email_docente)");
    $stmt->bindParam(':email_docente', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $messaggi = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <table class="table mt-3">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Email Studente</th>
            <th>Titolo Messaggio</th>
            <th>Azione</th> 
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($messaggi)): ?>
            <?php foreach ($messaggi as $messaggio): ?>
                <tr>
                    <td><?= htmlspecialchars($messaggio['id']) ?></td>
                    <td><?= htmlspecialchars($messaggio['email_studente']) ?></td>
                    <td><?= htmlspecialchars($messaggio['titolo_messaggio']) ?></td>
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal<?= $messaggio['id'] ?>">
                            Leggi Messaggio
                        </button>
                        <!-- Modal che mostra il testo del messaggio cliccato -->
                        <div class="modal fade" id="modal<?= $messaggio['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalLabel<?= $messaggio['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel<?= $messaggio['id'] ?>"><?= htmlspecialchars($messaggio['titolo_messaggio']) ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <?= nl2br(htmlspecialchars($messaggio['testo'])) ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($messaggio['data']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Non ci sono messaggi.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <button type="button" class="btn btn-secondary" onclick="location.href='dashboardDocente.php'">Torna indietro</button>
</div>
</body>
</html>
