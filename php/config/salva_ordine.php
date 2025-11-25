<?php
header('Content-Type: application/json');
session_start();

require_once "../config/conf.php";
require_once "../class/Ordine.php";

$data = json_decode(file_get_contents("php://input"), true);

// controllo dati mancanti
if(!$data || !isset($data['tavolo'], $data['piatti'])){
    echo json_encode(["success" => false, "message" => "Dati mancanti"]);
    exit;
}

// controllo se utente è loggato
if(!isset($_SESSION['user_id'])){
    echo json_encode(["success" => false, "message" => "Utente non loggato"]);
    exit;
}

$tavolo = intval($data['tavolo']);
$note = $data['note'] ?? "";
$piatti = $data['piatti'];

$ordine = new Ordine();
$id = $ordine->creaOrdine($tavolo, $piatti, $note);

// fallito
if($id === 0 || $id === false){
    echo json_encode(["success" => false, "message" => "Errore durante la creazione dell'ordine"]);
    exit;
}

// successo
echo json_encode(["success" => true, "message" => "Ordine creato!", "id_ordine" => $id]);
?>