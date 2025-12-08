<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . "/../includes/db.php";
require_once "../config/conf.php";

$data = json_decode(file_get_contents("php://input"), true);

// Controllo campi obbligatori
if (
    !$data ||
    !isset($data['nome'], $data['telefono'], $data['data'], $data['ora'], $data['persone'])
) {
    echo json_encode(["success" => false, "message" => "Dati mancanti"]);
    exit;
}

$nome      = trim($data['nome']);
$telefono  = trim($data['telefono']);
$dataPren  = $data['data'];  // es: 2025-12-05
$oraPren   = $data['ora'];   // es: 20:00
$persone   = intval($data['persone']);
$note      = isset($data['note']) ? trim($data['note']) : "";

// Controllo minimo su persone
if ($persone <= 0) {
    echo json_encode(["success" => false, "message" => "Numero di persone non valido"]);
    exit;
}

// Controllo formato data base-base (YYYY-MM-DD)
$d = DateTime::createFromFormat('Y-m-d', $dataPren);
if (!$d || $d->format('Y-m-d') !== $dataPren) {
    echo json_encode(["success" => false, "message" => "Data non valida"]);
    exit;
}

try {
    /** @var PDO $pdo */
    $stmt = $pdo->prepare("
        INSERT INTO prenotazione (nome, telefono, `data`, `ora`, persone, note, stato)
        VALUES (:nome, :telefono, :data, :ora, :persone, :note, 'in_attesa')
    ");

    $stmt->execute([
        ":nome"     => $nome,
        ":telefono" => $telefono,
        ":data"     => $dataPren,
        ":ora"      => $oraPren,
        ":persone"  => $persone,
        ":note"     => $note
    ]);

    $id = $pdo->lastInsertId();

    echo json_encode([
        "success" => true,
        "message" => "Prenotazione inviata!",
        "id_prenotazione" => $id
    ]);
} catch (Exception $e) {
    error_log("Errore prenotazione: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Errore durante la creazione della prenotazione"
    ]);
}