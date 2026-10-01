<?php
require 'esql.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indice</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Seleziona il tuo ruolo</h2>
    <div class="row mt-3">
        <div class="col-md-6">
            <h3>Studenti</h3>
            <a href="login_studente.php" class="btn btn-primary">Login Studente</a>
            <a href="signup_studente.php" class="btn btn-success">Registrazione Studente</a>
        </div>
        <div class="col-md-6">
            <h3>Docenti</h3>
            <a href="login_docente.php" class="btn btn-primary">Login Docente</a>
            <a href="signup_docente.php" class="btn btn-success">Registrazione Docente</a>
        </div>
    </div>
</div>
</body>
</html>
