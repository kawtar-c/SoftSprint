<?php
session_start();

//Accesso solo cameriere
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'cameriere') {
    header("Location: login.php");
    exit();
}

require_once "../php/includes/header.php";
$header = new Header();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Prenotazioni - Cameriere</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<?php echo $header->render('user'); ?>

<h1 class="page-title">Gestione Prenotazioni</h1>

<!-- Qui apparira la tabella delle prenotazioni -->
<div id="tabellone"></div>

<!-- Pulsante per aggiungere una nuova prenotazione -->
<button id="btnNuovaPrenotazione" class="btn-primary">+ Nuova Prenotazione</button>

</body>
</html>