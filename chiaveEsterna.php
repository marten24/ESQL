<?php
require 'conn.php';
session_start();

// Variabili
$attributoTipo = $_SESSION['attributoTipo'];
$attributoNome = $_SESSION['attributoNome'];
$nomeRidottoTabellaCreata = $_SESSION['nomeTabella'];
$nomeTabellaCreata = "Tabella_di_esercizio_" . $nomeRidottoTabellaCreata;
$nomeRidottoTabella = $_SESSION['valoreSelezionato'];
$nomeTabella = "Tabella_di_esercizio_" . $nomeRidottoTabella;

// Chiamata alla SP VisualizzaChiaviPrimarieTabellaSQL
$stmt = $pdo->prepare("CALL VisualizzaChiaviPrimarieTabellaSQL(:nomeTabella)");
$stmt->bindParam(':nomeTabella', $nomeTabella);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

$errore= '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attributoChiaveEsterna = $_POST['attributoChiaveEsterna'];

    try {
        // Chiamata alla SP AggiungiChiaveEsterna
        $stmt = $pdo->prepare("CALL AggiungiChiaveEsterna(:nomeTabellaCreata, :nomeTabella, :attributoNome, :attributoTipo, :attributoChiaveEsterna)");
        $stmt->bindParam(":nomeTabellaCreata", $nomeTabellaCreata, PDO::PARAM_STR);
        $stmt->bindParam(":nomeTabella", $nomeTabella, PDO::PARAM_STR);
        $stmt->bindParam(":attributoNome", $attributoNome, PDO::PARAM_STR);
        $stmt->bindParam(":attributoTipo", $attributoTipo, PDO::PARAM_STR);
        $stmt->bindParam(":attributoChiaveEsterna", $attributoChiaveEsterna, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        header("Location: inserisciAltriAttributi.php");
        exit();
    } catch (PDOException $e) {
        $errore = "Errore durante la creazione della foreign key, controlla che il tipo della variabile sia corretto";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiave Esterna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body{
        background-color: 	#e0ffff;
    }
</style>
<body class="text-center">
<div class="container mt-3">
    <h1>Hai selezionato la tabella: <u><?php echo $_SESSION['valoreSelezionato']; ?></u></h1>
    <p>Di quale attributo è chiave esterna l'attributo che hai appena inserito?</p>
    <?php if ($errore != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errore; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['valoreSelezionato'])): ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group d-flex justify-content-center">
                <label for="attributoChiaveEsterna"></label>
                <select class="form-control col-4" id="attributoChiaveEsterna" name="attributoChiaveEsterna" required>
                    <?php foreach ($result as $row): ?>
                        <?php
                        // Estrai i nomi delle colonne chiave primarie
                        $chiaviPrimarie = explode(',', $row['ChiaviPrimarie']);
                        ?>
                        <?php foreach ($chiaviPrimarie as $chiavePrimaria): ?>
                            <option value="<?php echo $chiavePrimaria; ?>"><?php echo $chiavePrimaria; ?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Conferma</button>
        </form>
    <?php else: ?>
        <p>Nessun valore selezionato.</p>
    <?php endif; ?>
</div>
</body>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>
