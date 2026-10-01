<?php
require 'conn.php';
session_start();

$errore = '';

// Recupero variabili
$num_righe = $_SESSION['numRighe'];
$nome_tabella = $_SESSION['nomeTabella'];

$query = "SHOW COLUMNS FROM Tabella_di_Esercizio_$nome_tabella";
$result = $pdo->query($query);

// Chiamata alla SP GetForeignKeys
$stmt = $pdo->prepare("CALL GetForeignKeys(:nomeTabella)");
$stmt->bindParam(":nomeTabella", $nome_tabella, PDO::PARAM_STR);
$stmt->execute();
$foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Filtro solo i valori che sono foreign key
$foreignKeysWithReferences = array_filter($foreignKeys, function ($row) {
    return !empty($row['REFERENCED_TABLE_NAME']);
});

// Inizializzo un array di chiavi
$keys = [];

// Se il modulo è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Inizia una transazione
        $pdo->beginTransaction();

        // Costruisco l'elenco dei campi escludendo 'id'
        $campi = array_filter(array_column($result->fetchAll(PDO::FETCH_ASSOC), 'Field'), function ($campo) {
            return $campo !== 'id';
        });

        // Per ogni riga
        for ($i = 0; $i < $num_righe; $i++) {
            // Costruisco i segnaposto per i campi
            $segnaposto = implode(', ', array_map(function ($campo) use ($i) {
                return ':' . $campo . $i;
            }, $campi));

            // Query SQL
            $sql = "INSERT INTO Tabella_di_Esercizio_$nome_tabella (" . implode(', ', $campi) . ") VALUES ($segnaposto)";
            $inserimento = $pdo->prepare($sql);

            // Associo i valori ai segnaposto
            foreach ($campi as $campo) {
                $campo_nome = $campo . $i;
                $inserimento->bindValue(':' . $campo . $i, $_POST[$campo_nome]);
            }

            // Eseguo l'inserimento nel database
            if (!$inserimento->execute()) {
                throw new Exception("Errore nell'inserimento dei dati nella tabella.");
            }
        }
        $pdo->commit();

        // Mostra un popup utilizzando JavaScript
        echo '<script>';
        echo 'alert("Dati inseriti correttamente nella tabella.");';
        echo 'window.addEventListener("load", function() {';
        echo '    var popup = window.open("", "_self");';
        echo '    window.location.href = "tabelleSQL.php";';
        echo '});';
        echo '</script>';

    } catch (Exception $e) {
        $pdo->rollBack();
        $errore = "Errore: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Dati</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: 	#e0ffff;
        }
    </style>
</head>
<body>
<div class="container mt-3">
    <h1 class="text-center">Inserisci i dati nella tabella</h1>
    <br>
    <?php if ($errore != ''): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $errore; ?>
        </div>
        <h5 class="text-center">Torna indietro e reinserisci i valori</h5>
    <?php else: ?>
        <div class="form-group d-flex justify-content-center">
            <div class="col-md-5">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <?php
                    for ($i = 0; $i < $num_righe; $i++) {
                        // Se la tabella ha delle colonne
                        if ($result) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $campo = $row['Field'];
                                $tipo_dato = $row['Type'];

                                // Itero sulle foreign key salvando tabella e colonna di riferimento
                                foreach ($foreignKeysWithReferences as $foreignKey) {
                                    $referencedTableName = $foreignKey['REFERENCED_TABLE_NAME'];
                                    $referencedColumnName = $foreignKey['REFERENCED_COLUMN_NAME'];

                                    // Verifico se il campo corrente corrisponde alla colonna referenziata
                                    if ($foreignKey['COLUMN_NAME'] === $campo) {
                                        $queryGetKeys = "SELECT $referencedColumnName FROM $referencedTableName";
                                        $getKeysResult = $pdo->query($queryGetKeys);
                                        $keys[$referencedTableName] = $getKeysResult->fetchAll(PDO::FETCH_COLUMN);
                                    }
                                }

                                // Escludo il campo 'id' dalla visualizzazione
                                if ($campo !== 'id') {
                                    echo '<div class="form-group">';
                                    echo '<label for="' . $campo . '">' . '(' . ($i + 1) . ') ' . $campo . '</label>';

                                    // Verifico se l'attributo è una chiave esterna
                                    $isForeignKey = false;
                                    foreach ($foreignKeysWithReferences as $foreignKey) {
                                        if ($foreignKey['COLUMN_NAME'] === $campo) {
                                            $isForeignKey = true;
                                            $referencedTableName = $foreignKey['REFERENCED_TABLE_NAME'];
                                            $referencedColumnName = $foreignKey['REFERENCED_COLUMN_NAME'];

                                            // Verifico se ci sono valori per la chiave esterna
                                            if (isset($keys[$referencedTableName])) {
                                                // Mostra il menu a discesa con i valori della chiave esterna
                                                echo '<select class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                                foreach ($keys[$referencedTableName] as $keyValue) {
                                                    echo '<option value="' . $keyValue . '">' . $keyValue . '</option>';
                                                }
                                                echo '</select>';
                                            } else {
                                                echo 'Nessun valore trovato per la chiave esterna.';
                                            }
                                            break;
                                        }
                                    }
                                    if (!$isForeignKey) {
                                        // Distinzione in base al tipo di dato
                                        if (strpos($tipo_dato, 'varchar') !== false) {
                                            // Se il tipo di dato contiene 'varchar', utilizza un input di tipo testo
                                            echo '<input type="text" class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                        } elseif (strpos($tipo_dato, 'tinyint') !== false) {
                                            // Se il tipo di dato è boolean, utilizza un menu a discesa per selezionare true o false
                                            echo '<select class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                            echo '<option value="1">True</option>';
                                            echo '<option value="0">False</option>';
                                            echo '</select>';
                                        } elseif (strpos($tipo_dato, 'int') !== false) {
                                            // Se il tipo di dato contiene 'int' utilizza un input di tipo numero
                                            echo '<input type="number" class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                        } elseif (strpos($tipo_dato, 'date') !== false) {
                                            // Se il tipo di dato contiene 'date', utilizza un input di tipo data
                                            echo '<input type="date" class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                        } else {
                                            // Se il tipo di dato non rientra nelle precedenti categorie, utilizza un input di tipo testo
                                            echo '<input type="text" class="form-control" id="' . $campo . $i . '" name="' . $campo . $i . '" required>';
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                            $result->execute();
                        }
                    }
                    ?>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary mr-1" style="width: 150px;">Invia</button>
                        <a href="tabelleSQL.php" class="btn btn-danger">Indietro</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
