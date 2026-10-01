<?php
require 'conn.php';
session_start();

$errore = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $codice = $_POST['codice'];
    $anno_immatricolazione = $_POST['anno_immatricolazione'];
    $password = $_POST['password'];

    try {
        // Chiamata alla stored procedure per verificare l'esistenza dell'email in Docente
        $stmt = $pdo->prepare("CALL VerificaEmailEsistenteDocente(:email)");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $stmt->fetchAll(); // Recupera i risultati per assicurarti che siano stati completamente elaborati.
        $stmt->closeCursor(); // Chiudi il cursore per liberare la connessione.

// Chiamata alla stored procedure per verificare l'esistenza dell'email in Studente
        $stmt1 = $pdo->prepare("CALL VerificaEmailEsistenteStudente(:email)");
        $stmt1->bindParam(':email', $email);
        $stmt1->execute();
        $stmt1->fetchAll(); // Anche qui, assicurati di recuperare i risultati.
        $stmt1->closeCursor(); // Chiudi il cursore prima di procedere.

        if ($stmt->rowCount() == 0 && $stmt1->rowCount()==0 ) {
            // Chiamata alla stored procedure per inserire lo studente
            $stmt = $pdo->prepare("CALL InserisciStudente(:email, :nome, :cognome, :codice, :anno_immatricolazione, :password)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cognome', $cognome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':codice', $codice);
            $stmt->bindParam(':anno_immatricolazione', $anno_immatricolazione);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Chiamata alla stored procedure per inserire il telefono, se fornito
            if ($telefono != '') {
                $stmt = $pdo->prepare("CALL InserisciTelefonoStudente(:email, :telefono)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->execute();
            }

            $_SESSION['user_email'] = $email;
            header("Location: dashboardStudente.php");
            exit();
        } else {
            $errore = 'L\'email inserita è già registrata.';
        }
    } catch(PDOException $e) {
        $errore = "Errore durante la registrazione: " . $e->getMessage();
    }

    $pdo = null;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Studente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Registrazione Studente</h2>
    <?php if ($errore != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errore; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="cognome">Cognome:</label>
            <input type="text" class="form-control" id="cognome" name="cognome" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="telefono">Telefono (opzionale):</label>
            <input type="text" class="form-control" id="telefono" name="telefono" >
        </div>
        <div class="form-group">
            <label for="codice">Codice Studente:</label>
            <input type="text" class="form-control" id="codice" name="codice" required pattern="[A-Za-z0-9]{16}" title="Il codice deve essere alfanumerico e di lunghezza esattamente 16 caratteri.">
        </div>

        <div class="form-group">
            <label for="anno_immatricolazione">Anno di Immatricolazione:</label>
            <input type="number" class="form-control" id="anno_immatricolazione" name="anno_immatricolazione" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <br>
            <button type="submit" class="btn btn-primary">Registrati</button>
            <a href="index.php" class="btn btn-secondary">Torna Indietro</a>

    </form>
</div>
</body>
</html>
