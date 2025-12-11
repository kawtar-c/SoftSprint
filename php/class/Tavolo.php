<?php
require_once __DIR__ . "/../includes/db.php";

class Tavolo {

    private $db;

    public function __construct($db=null) {
        $this->db = $db ?? open();
    }

    //Ottiene tutti i tavoli
    public function getTavoli(): array {
        $query = "SELECT * FROM tavolo ORDER BY numero ASC";
        $req= $this->db->query($query);
        return $req->fetch_all(MYSQLI_ASSOC);
    }

    //Aggiorna stato tavolo
    public function setStato(int $idTavolo, string $stato): bool {
        $query = "UPDATE tavolo SET stato = ? WHERE id_tavolo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $stato, $idTavolo);
        return $stmt->execute();
    }

    //Ottiene stato singolo tavolo
    public function getStato(int $idTavolo): ?string {
        $query = "SELECT stato FROM tavolo WHERE id_tavolo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idTavolo);
        $result = $stmt->get_result()->fetch_assoc();
        return $result["stato"] ?? null;
    }
}
