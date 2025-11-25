<?php
require_once __DIR__ . "/../includes/db.php";

class Tavolo {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    //Ottiene tutti i tavoli
    public function getTavoli(): array {
        $query = "SELECT * FROM tavolo ORDER BY numero ASC";
        $req= $this->db->query($query);
        return $req->fetch_all(MYSQLI_ASSOC);
    }

    //Aggiorna stato tavolo (es. occupato, libero, in preparazione)
    public function setStato(int $idTavolo, string $stato): bool {
        $query = "UPDATE tavoli SET stato = :stato WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ":stato" => $stato,
            ":id" => $idTavolo
        ]);
    }

    //Ottiene stato singolo tavolo
    public function getStato(int $idTavolo): ?string {
        $query = "SELECT stato FROM tavoli WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([":id" => $idTavolo]);
        return $stmt->fetchColumn();
    }
}
