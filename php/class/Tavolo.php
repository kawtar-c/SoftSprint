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

    //Ottiene singolo tavolo
    public function getTavolo(int $idTavolo): ?array {
        $query = "SELECT * FROM tavolo WHERE id_tavolo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idTavolo);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
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

    // Ottieni Capacita
    public function setCapacitaMassima(int $idTavolo, int $capacita): bool {
       if ($capacita <= 0) return false;
    
       $query = "UPDATE tavolo SET capacita_max = ? WHERE id_tavolo = ?";
       $stmt = $this->db->prepare($query);
       $stmt->bind_param("ii", $capacita, $idTavolo);
       return $stmt->execute();
    }

    // Modifica Tavolo
    public function modificaTavolo(int $idTavolo, int $numero, int $capacita_max, string $stato): bool {
        if ($capacita_max <= 0 || $numero <= 0) return false;

        $query = "UPDATE tavolo SET numero = ?, capacita_max = ?, stato = ? WHERE id_tavolo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iisi", $numero, $capacita_max, $stato, $idTavolo);
        return $stmt->execute();
    }

    // Aggiungi Tavolo
    public function aggiungiTavolo(int $numero, int $capacita_max, string $stato): bool {
        if ($capacita_max <= 0 || $numero <= 0) return false;

        $query = "INSERT INTO tavolo (numero, capacita_max, stato) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $numero, $capacita_max, $stato);
        return $stmt->execute();
    }

    // Elimina Tavolo
    public function eliminaTavolo(int $idTavolo): bool {
        $query = "DELETE FROM tavolo WHERE id_tavolo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $idTavolo);
        return $stmt->execute();
    }
}
