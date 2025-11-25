<?php
require_once __DIR__ . "/../includes/db.php";

class OrdinePiatto {

    private $db;

    public function __construct() {
        $this->db = open();
    }

    // Aggiunge piatto con quantità
    public function aggiungiPiatto(int $idOrdine, int $idPiatto, int $qty) {
        $sql = "INSERT INTO ordine_piatto (id_ordine, id_piatto, quantita) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $idOrdine, $idPiatto, $qty);
        $stmt->execute();
        $stmt->close();
    }

    // Restituisce elenco piatti con nome, prezzo e quantità
    public function getPiatti(int $idOrdine): array {
        $sql = "SELECT p.id_piatto, p.nome, p.prezzo, op.quantita
                FROM ordine_piatto op
                JOIN piatto p ON op.id_piatto = p.id_piatto
                WHERE op.id_ordine = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idOrdine);
        $stmt->execute();

        $result = $stmt->get_result();
        $piatti = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();
        return $piatti;
    }
}
