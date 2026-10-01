<?php
session_start(); // Avvia la sessione
require 'conn.php';

// Variabili
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Chiamata alla SP TrovaStudenteConEmailePassword
        $stmt = $pdo->prepare("CALL TrovaStudenteConEmailePassword(:email, :password)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['user_email'] = $email;
            header("Location: dashboardStudente.php");
            exit();
        } else {
            $errore = 'Credenziali non valide.';
        }
    } catch(PDOException $e) {
        $errore = "Errore durante il login: " . $e->getMessage();
    }
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Studente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Login Studente</h2>
    <?php if (!empty($errore)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errore; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="index.php" class="btn btn-secondary">Torna Indietro</a>

    </form>
</div>
</body>
</html>
