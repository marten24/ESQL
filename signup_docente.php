<?php
require 'conn.php';
session_start();
$errore = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $nome_dipartimento = $_POST['nome_dipartimento'];
    $nome_corso = $_POST['nome_corso'];
    $password = $_POST['password'];

    try {
        // Chiamata alla stored procedure per verificare l'esistenza dell'email in Docente
        $stmt = $pdo->prepare("CALL VerificaEmailEsistenteDocente(:email)");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $stmt->fetchAll(); 
        $stmt->closeCursor(); 

// Chiamata alla stored procedure per verificare l'esistenza dell'email in Studente
        $stmt1 = $pdo->prepare("CALL VerificaEmailEsistenteStudente(:email)");
        $stmt1->bindParam(':email', $email);
        $stmt1->execute();
        $stmt1->fetchAll(); 
        $stmt1->closeCursor();


        if ($stmt->rowCount() == 0 && $stmt1->rowCount()==0) {
            $stmt = $pdo->prepare("CALL InserisciDocente(:email, :nome, :cognome, :nome_dipartimento, :nome_corso, :password)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cognome', $cognome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nome_dipartimento', $nome_dipartimento);
            $stmt->bindParam(':nome_corso', $nome_corso);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Inserisci il numero di telefono se è stato fornito
            if ($telefono != '') {
                $stmt = $pdo->prepare("CALL InserisciTelefonoDocente(:email, :telefono)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->execute();
            }

            $_SESSION['user_email'] = $email; 
            header("Location: dashboardDocente.php"); 
            exit();
        } else {
            $errore = 'L\'email inserita è già registrata.';
        }
    } catch (PDOException $e) {
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
    <title>Registrazione Docente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Registrazione Docente</h2>
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
            <label for="nomedip">Nome dipartimento di afferenza:</label>
            <input type="text" class="form-control" id="nome_dipartimento" name="nome_dipartimento" required>
        </div>
        <div class="form-group">
            <label for="nome_corso">Nome corso di cui si è titolari :</label>
            <input type="text" class="form-control" id="nome_corso" name="nome_corso" required>
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
