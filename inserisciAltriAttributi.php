<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Attributi</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{
            background-color: 	#e0ffff;
        }
        .loader {
            font-size: 2em;
            font-weight: 900;
        }
        .loader > * {
            color: black;
        }
        .loader span {
            display: inline-flex;
        }
        .loader span:nth-child(2) {
            letter-spacing: -1em;
            overflow: hidden;
            animation: reveal 1500ms cubic-bezier(0.645, 0.045, 0.355, 1) infinite
            alternate;
        }
        @keyframes reveal {
            0%,
            100% {
                opacity: 0.5;
                letter-spacing: -1em;
            }
            50% {
                opacity: 1;
                letter-spacing: 0em;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5 d-flex justify-content-center">
    <h1>Desideri aggiungere altri attributi?</h1>
</div>
<div class="container mt-3 d-flex justify-content-center">
    <div class="row">
        <div class="col">
            <a href="inserisciAttributiTabelle.php" class="btn btn-primary"  style="width: 150px;">Sì</a>
        </div>
        <div class="col">
            <a href="popolaTabella.php" class="btn btn-danger btn-block"  style="width: 150px;">No</a>
        </div>
    </div>
</div>
<div class="container mt-5 d-flex justify-content-center">
    <div class="loader">
        <span>&lt;/</span>
        <span>LOADING</span>
        <span>/&gt;</span>
    </div>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
