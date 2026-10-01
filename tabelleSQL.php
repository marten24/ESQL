<?php
require 'conn.php';
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: loginDocente.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azioni Tabelle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Scegli azione da eseguire!</h1>
    <br>
    <div class="row">
        <div class="col">
            <a href="creaTabellaSQL.php" class="btn btn-primary">Crea tabella SQL</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="visualizzaTabelleSQL.php" class="btn btn-primary">Visualizza/Modifica tabella SQL</a>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <a href="dashboardDocente.php" class="btn btn-danger">Indietro</a>
        </div>
    </div>
</div>
</body>
</html>
