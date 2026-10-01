<?php
require 'conn.php';
session_start();

$errore = '';

// Recupero le variabili
$user_email = $_SESSION['user_email'];
$nome_tabella = $_SESSION['nomeTabella'];

// Chiamata alla SP VisualizzaTabelleSQL
$stmt = $pdo->prepare("CALL VisualizzaTabelleSQL(:user_email)");
$stmt->bindParam(':user_email', $user_email);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Variabili
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attributoNome = $_POST['attributoNome'];
    $attributoTipo = $_POST['attributoTipo'];
    $chiavePrimaria = $_POST['chiavePrimaria'];
    $chiaveEsterna = $_POST['chiaveEsterna'];
    $_SESSION['valoreSelezionato'] = $_POST['attributoChiaveEsterna'];
    $_SESSION['attributoTipo'] = $_POST['attributoTipo'];
    $_SESSION['attributoNome'] = $_POST['attributoNome'];


    try {
        // Chaimata alla SP AggiungiColonna per aggiungere una colonna alla tabella
        $nomeCompletoTabella=  "Tabella_di_esercizio_" . $nome_tabella;
        $stmt = $pdo->prepare("CALL AggiungiColonna(:nomeCompletoTabella, :attributoNome, :attributoTipo)");
        $stmt->bindParam(":nomeCompletoTabella", $nomeCompletoTabella, PDO::PARAM_STR);
        $stmt->bindParam(":attributoNome", $attributoNome, PDO::PARAM_STR);
        $stmt->bindParam(":attributoTipo", $attributoTipo, PDO::PARAM_STR);
        $stmt->execute();

        // Se la colonna deve essere parte di una chiave primaria composta
        if ($chiavePrimaria == 1) {
            // Ottengo la struttura della tabella
            $descrizioneTabellaSQL = "DESCRIBE Tabella_di_Esercizio_$nome_tabella";
            $descrizioneTabella = $pdo->query($descrizioneTabellaSQL)->fetchAll(PDO::FETCH_ASSOC);

            // Inizializzo un array per i valori delle chiavi primarie
            $chiaviPrimarie = [];

            // Scansiono le informazioni sulla tabella per trovare le chiavi primarie
            foreach ($descrizioneTabella as $colonna) {
                // La colonna è parte della chiave primaria
                if ($colonna['Key'] === 'PRI') {
                    // La aggiungo all'array delle chiavi
                    $chiaviPrimarie[] = $colonna['Field'];
                }
            }

            // Unisco i valori delle chiavi primarie dall'array
            $valoriChiaviPrimarie = implode(', ', $chiaviPrimarie);

            // Aggiungo la nuova chiave primaria con i valori dell'array
            $stmt = $pdo->prepare("CALL AggiungiChiavePrimariaSQL(:nomeCompletoTabella, :valoriChiaviPrimarie, :attributoNome)");
            $stmt->bindParam(":nomeCompletoTabella", $nomeCompletoTabella, PDO::PARAM_STR);
            $stmt->bindParam(":valoriChiaviPrimarie", $valoriChiaviPrimarie, PDO::PARAM_STR);
            $stmt->bindParam(":attributoNome", $attributoNome, PDO::PARAM_STR);
            $stmt->execute();

            // Aggiungo vincolo di unicità
            $stmt = $pdo->prepare("CALL AggiungiVincoloUnicita(:nomeCompletoTabella, :attributoNome)");
            $stmt->bindParam(":nomeCompletoTabella", $nomeCompletoTabella, PDO::PARAM_STR);
            $stmt->bindParam(":attributoNome", $attributoNome, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Chiamata alla stored procedure InserisciAttributo per inserire l'attributo nella tabella
        $stmt = $pdo->prepare("CALL InserisciAttributo(:attributoNome, :attributoTipo)");
        $stmt->bindParam(':attributoNome', $attributoNome);
        $stmt->bindParam(':attributoTipo', $attributoTipo);
        $stmt->execute();

        // Recupero l'id dell'attributo
        $stmtSelect = $pdo->query("SELECT LAST_INSERT_ID() as last_id");
        $lastInsertId = $stmtSelect->fetch(PDO::FETCH_ASSOC)['last_id'];

        // Chaiamta all SP InserisciTabellaSQLAttributo
        $stmt2 = $pdo->prepare("CALL InserisciTabellaSQLAttributo(:id_attributo, :nome_tabella)");
        $stmt2->bindParam(':id_attributo', $lastInsertId);
        $stmt2->bindParam(':nome_tabella', $nome_tabella);
        $stmt2->execute();

        // Chiamata alla SP InserisciAttributoChiavePrimaria per inserire nella tabella chiave primaria attributo
        $stmt3 = $pdo->prepare("CALL InserisciAttributoChiavePrimaria(:id_attributo, :chiave_primaria)");
        $stmt3->bindParam(':id_attributo', $lastInsertId);
        $stmt3->bindParam(':chiave_primaria', $chiavePrimaria);
        $stmt3->execute();

        // Chiamata alla SP InserisciAttributoVincoloIntegrita per inserire nella tabella chiave esterna attributo
        $stmt4 = $pdo->prepare("CALL InserisciAttributoVincoloIntegrita(:id_attributo, :chiaveEsterna)");
        $stmt4->bindParam(':id_attributo', $lastInsertId);
        $stmt4->bindParam(':chiaveEsterna', $chiaveEsterna);
        $stmt4->execute();

        if ($_POST['chiaveEsterna'] === '1') {
            header("Location: chiaveEsterna.php");
        } else {
            header("Location: inserisciAltriAttributi.php");
        }
        exit();
    } catch (PDOException $e) {
        $errore = "Errore durante la creazione dell'attributo: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Attributo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-3 d-flex justify-content-center">
    <div>
        <h1>Assegna attributi alla tabella</h1>
        <br>
        <?php if ($errore != ''): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errore; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="attributoNome">Nome dell'Attributo:</label>
                <input type="text" class="form-control" id="attributoNome" name="attributoNome" required>
            </div>
            <div class="form-group">
                <label for="attributoTipo">Tipo dell'Attributo:</label>
                <select class="form-control" id="attributoTipo" name="attributoTipo" required>
                    <option value="varchar(255)">VARCHAR</option>
                    <option value="INTEGER">INTEGER</option>
                    <option value="date">DATE</option>
                    <option value="boolean">BOOLEAN</option>
                </select>
            </div>
            <div class="form-group">
                <label for="chiavePrimaria">E' chiave primaria?</label>
                <select class="form-control" id="chiavePrimaria" name="chiavePrimaria" required>
                    <option value="0">FALSE</option>
                    <option value="1">TRUE</option>
                </select>
            </div>
            <div class="form-group">
                <label for="chiaveEsterna">E' chiave esterna?</label>
                <select class="form-control" id="chiaveEsterna" name="chiaveEsterna" required
                        onchange="showForeignKey()">
                    <option value="0">NO</option>
                    <option value="1">SI</option>
                </select>
            </div>
            <div class="form-group" id="foreign-key-option" style="display: none;">
                <label for="attributoChiaveEsterna">Seleziona di quale tabella è chiave esterna:</label>
                <select class="form-control" id="attributoChiaveEsterna" name="attributoChiaveEsterna" onchange="tabellaSelezionata()">
                    <?php foreach ($result as $row): ?>
                        <?php if ($row['NOME'] !== $nome_tabella): ?>
                            <option value="<?php echo $row['NOME']; ?>"><?php echo $row['NOME']; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Inserisci</button>
            </div>

        </form>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function tabellaSelezionata() {
        const attributoChiaveEsternaSelect = document.getElementById("attributoChiaveEsterna");
        const valoreSelezionato = attributoChiaveEsternaSelect.value;
        // Aggiorna l'elemento HTML con il valore selezionato
        document.getElementById("valoreSelezionato").innerText = "Valore selezionato: " + valoreSelezionato;
    }

    function showForeignKey() {
        const chiaveEsternaSelect = document.getElementById("chiaveEsterna");
        const foreignKeyOption = document.getElementById("foreign-key-option");
        if (chiaveEsternaSelect.value === "1") {
            foreignKeyOption.style.display = "block";
        } else {
            foreignKeyOption.style.display = "none";
            hideAttributi();
        }
    }

    function hideAttributi() {
        document.getElementById("attributi-option").style.display = "none";
    }
</script>
</body>
</html>
