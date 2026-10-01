<?php

$host = 'localhost';
$dbname = 'ESQL';
$username = 'root'; // Nome utente del database
$password = 'root'; // Password del database

try {
    // Crea una nuova connessione PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Imposta l'attributo per generare eccezioni in caso di errori
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
