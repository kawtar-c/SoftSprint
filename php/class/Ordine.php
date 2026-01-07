<?php
require_once __DIR__ . "/../includes/db.php";
require_once "OrdinePiatto.php";

class Ordine {

    private $db;
    private $ordinePiatto;

    //Accetta un OrdinePiatto “esterno” (mock nei test)
    public function __construct($db = null, $ordinePiatto = null) {
        $this->db =$db ?? open();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //debug sicuro

        $this->ordinePiatto = $ordinePiatto ?: new OrdinePiatto();
    }

    // Inserisce nuovo ordine
    public function creaOrdine(int $idTavolo, array $piatti, string $note = null): int {
        if(!isset($_SESSION['user_id'])) {
            return 0;
        }

        $sql = "INSERT INTO ordine (id_tavolo, data_ora, stato, id_utente, note) 
                VALUES (?, NOW(), 'inviato', ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $idTavolo, $_SESSION['user_id'], $note);
        $stmt->execute();

        $idOrdine = $this->getInsertId();
        $stmt->close();

       // Us al’oggetto iniettato (mock nei test)
        foreach ($piatti as $p) {
            $this->ordinePiatto->aggiungiPiatto($idOrdine, $p["id"], $p["qty"]);
        }

        return (int)$idOrdine;
    }

    // Ottieni ordine
    public function getIdOrdinedaTavolo(int $idTavolo): ?int {
        $sql = "SELECT id_ordine FROM ordine WHERE id_tavolo = ? ORDER BY data_ora DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idTavolo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? (int)$row['id_ordine'] : null;
    }

    // Chiusura ordine
    public function chiudiOrdine(int $idOrdine, float $totale): bool {
        $sql = "UPDATE ordine SET stato = 'completato', totale = ? WHERE id_ordine = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("di", $totale, $idOrdine);
        return $stmt->execute();
    }

    // Ritorna tutti gli ordini non conclusi
    public function getOrdiniAttivi(): array {
        $sql = "SELECT * FROM ordine WHERE stato != 'completato' ORDER BY data_ora ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Cambia stato ordine
    public function cambiaStato(int $idOrdine, string $stato): bool {
        $sql = "UPDATE ordine SET stato = ? WHERE id_ordine = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $stato, $idOrdine);
        return $stmt->execute();
    }

    // Carica piatti
    public function getPiattiDaOrdine(int $idTavolo): array {
        $sql = "SELECT id_ordine FROM ordine WHERE id_tavolo = ? and stato != 'completato' ORDER BY data_ora DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $idTavolo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if (!$row) {
            return [];
        }
        //USO DELL’OGGETTO INIETTATO 
        return $this->ordinePiatto->getPiatti($row['id_ordine']);
    }

    // Ottini ordini pronti
    public function getOrdiniPronti(): array {
        $sql = "SELECT * FROM ordine WHERE stato = 'pronto' ORDER BY data_ora ASC LIMIT 2";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // NUOVO METODO MOCKABILE
    protected function getInsertId() {
        return $this->db->insert_id;
    }
}
