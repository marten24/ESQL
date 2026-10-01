<?php
require 'conn.php';
session_start();

$errore = '';

// Variabili
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomeTabella'])) {
    $_SESSION['nomeTabella'] = $_POST['nomeTabella'];
}

$nome_tabella = $_SESSION['nomeTabella'];

// Chiamata alla SP ritornaNumRighe
$stmt = $pdo->prepare("CALL ritornaNumRighe(:nomeTabella)");
$stmt->bindParam(':nomeTabella', $nome_tabella);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determina il numero di righe della tabella
$num_righe_attuali = !empty($result) ? $result[0]['num_righe'] : 0;

// Verifica se è stato inviato un nuovo valore di righe da visualizzare
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numRighe'])) {
    $numero_righe_selezionate = intval($_POST['numRighe']);
    $differenza = $numero_righe_selezionate - $num_righe_attuali;

    // Salvo in sessione
    $_SESSION['numRighe'] = $differenza;

    header("Location: popolaTabella.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Tabella</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: #e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <h1 class="text-center">Stai modificando la tabella <u><?php echo $nome_tabella; ?></u></h1>
    <br>
    <?php if (!empty($result)): ?>
        <?php if ($result[0]['num_righe'] == 1): ?>
            <h5 class="text-center">La tabella ha attualmente <?php echo $result[0]['num_righe']; ?> riga</h5>
        <?php else: ?>
            <h5 class="text-center">La tabella ha attualmente <?php echo $result[0]['num_righe']; ?> righe</h5>
        <?php endif ?>
    <?php else: ?>
        <h5 class="text-center">Impossibile ottenere il numero di righe della tabella</h5>
    <?php endif; ?>
    <?php if ($errore != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errore; ?>
        </div>
    <?php endif; ?>
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="text-center mt-3">
                <h4>Aggiungi Righe</h4>
            </div>
            <form action="" method="post">
                <div class="text-center">
                    <label for="numeroRighe">Determina il nuovo numero di righe della tabella:</label>
                    <input type="number" id="numRighe" name="numRighe" min="<?php echo $result[0]['num_righe'] + 1; ?>" value="1" class="form-control">
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Aggiungi</button>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class="text-center mt-3">
                <h4>Elimina Righe</h4>
            </div>
            <div class="text-center mt-3">
                <label for="numeroRighe">Clicca il bottone "Elimina" per rimuovere righe:</label>
            </div>
            <form action="rimuoviRigaTabellaSQL.php" method="post">
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-danger">Elimina</button>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center mt-3">
        <a href="visualizzaTabelleSQL.php" class="btn btn-secondary">Indietro</a>
    </div>
</div>
</body>
</html>
