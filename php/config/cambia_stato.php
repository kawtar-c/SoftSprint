<?php
require_once "../includes/session.php";
require_once "../class/Ordine.php";

header('Content-Type: application/json');

// Controllo ruolo
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'cuoco') {
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato']);
    exit;
}

// Recupera dati JSON inviati
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id'], $input['stato'])) {
    echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
    exit;
}

$id = intval($input['id']);
$stato = $input['stato'];

// Controllo validità stato
$statiValidi = ['nuovo', 'in preparazione', 'pronto'];
if (!in_array($stato, $statiValidi)) {
    echo json_encode(['success' => false, 'message' => 'Stato non valido']);
    exit;
}

// Aggiorna lo stato nel database
try {
    $ordine = new Ordine();
    $res = $ordine->cambiaStato($id, $stato); 

    if ($res) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore aggiornamento ordine']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
