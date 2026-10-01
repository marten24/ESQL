<?php
session_unset(); // Rimuovi tutte le variabili di sessione
session_destroy(); // Distruggi la sessione

header("Location: index.php"); // Reindirizza l'utente alla pagina di login
exit();
?>
